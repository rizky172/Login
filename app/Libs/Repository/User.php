<?php
namespace App\Libs\Repository;

use Auth;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Models\User as Model;
use App\Models\Media;

class User
{
    private $password;
    private $confirmPassword;
    private $photo;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function addPassword($password, $confirmPassword)
    {
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;
    }

    public function addPhoto($photo)
    {
        $this->photo = $photo;
    }

    public static function validOrThrow(Validator $validator)
    {
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateBasic()
    {
        $fields = [
            'id' => $this->model->id,
            'name' => $this->model->name,
            'username' => $this->model->username,
            'email' => $this->model->email,
            'role' => $this->model->role,
            'password' => $this->password,
            'confirm_password' => $this->confirmPassword
        ];

        $rules = [
            'id' => 'nullable',
            'name' => 'required',
            'username'=> 'required|unique:users,username,' . $this->model->id,
            'email'=> 'required|email|unique:users,email,' . $this->model->id,
            'role' => 'required',
            'password' => 'nullable|required_without:id|min:5',
            'confirm_password' => 'same:password',
        ];

        $messages = [
            'name.required' => 'Name wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah ada.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah ada.',
            'role.required' => 'Role wajib diisi.',
            'password.required_without' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 5 karakter.',
            'confirm_password.same' => 'Confirm Password tidak sama.',
        ];

        $validator = ValidatorFacade::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    private function validateBeforeDelete()
    {
        $user = Auth::user();
        $isUser = $this->model->id == $user->id ? 1 : 0;

        $fields = [
            'is_user' => $isUser
        ];

        $rules = [
            'is_user' => 'in:0'
        ];

        $messages = [
            'is_user.in' => 'Gagal hapus, data sedang digunakan untuk Login.'
        ];

        $validator = ValidatorFacade::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    private function generateData()
    {
        if(!empty($this->password)){
            $this->model->password = Hash::make($this->password);
        }
    }

    public function save()
    {
        $this->generateData();
        $this->validateBasic();

        $this->model->save();

        if(!empty($this->photo)){
            $this->savePhoto();
        }
    }

    private function generateFileName($file)
    {
        // To lower case
        $originalName = strtolower($file->getClientOriginalName());
        $originalExt = $file->getClientOriginalExtension();
        // Remove extension
        $originalName = str_replace(sprintf(".%s", $originalExt), '', $originalName);
        // No white space
        $originalName = str_replace(' ', '-', $originalName);
        // Remove symbol
        $originalName = preg_replace('/[^A-Za-z0-9\-]/', '_', $originalName);
        $originalName = sprintf("%s.%s", $originalName, $originalExt);

        $randomInt = rand(1, 9999) . time();

        $newFilename = ':id:_:timestamp:_:filename:';

        $newFilename = str_replace(':id:', $this->model->id, $newFilename);
        $newFilename = str_replace(':filename:', $originalName, $newFilename);
        $newFilename = str_replace(':timestamp:', $randomInt, $newFilename);

        return $newFilename;
    }

    private function savePhoto()
    {
        $filename = $this->generateFileName($this->photo);
        $folder = $this->model->username;
        $storagePath = $folder . '/' . $filename;

        $existing = Media::where('fk_id', $this->model->id)
                        ->where('table_name', $this->model->getTable())
                        ->first();

        if ($existing && $existing->name) {
            $oldPath = $username . '/' . $existing->name;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $this->photo->storeAs($folder, $filename, 'public');

        $media = Media::findOrNew($existing->id ?? null);
        $media->fk_id = $this->model->id;
        $media->table_name = $this->model->getTable();
        $media->name = $filename;
        $media->save();
    }

    public function toArray()
    {
        $data = $this->model->toArray();
        
        $media = Media::where('fk_id', $this->model->id)
                        ->where('table_name', $this->model->getTable())
                        ->first();

        $data['media'] = $media ? asset('storage/' . $this->model->username . '/' . $media->name) : null;

        return $data;
    }

    private function deleteFile()
    {
        $media = Media::where('fk_id', $this->model->id)
                        ->where('table_name', $this->model->getTable())
                        ->first();
        
        if ($media && $media->name) {
            $oldPath = $this->model->username . '/' . $media->name;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $media->delete();
        
    }

    public function delete($permanent = null)
    {
        $this->validateBeforeDelete();

        if(empty($permanent)){
            $this->model->delete();
        }else{
            $this->deleteFile();

            $this->model->forceDelete();
        }   
    }

    public function restore()
    {
        $this->model->restore();
    }  
}

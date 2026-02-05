<?php

namespace App\Imports;

use App\Models\User;
use App\Traits\CommonTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class UserImport implements ToCollection, WithStartRow
{
    use CommonTrait;

    private $errors = [];

    private $rows = 0;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function getErrors()
    {
        return $this->errors; // return all errors
    }

    public function rules(): array
    {
        return [
            '0' => 'required|max:100',
            '1' => 'required|max:200|email|unique:users,email,NULL,id,deleted_at,NULL',
            '2' => 'required|in:F,M',
        ];
    }

    public function validationMessages()
    {
        return [
            '0.required' => __('messages.user.validation.messsage.0.required'),
            '0.max' => __('messages.user.validation.messsage.0.max'),
            '1.required' => __('messages.user.validation.messsage.1.required'),
            '1.max' => __('messages.user.validation.messsage.1.max'),
            '1.email' => __('messages.user.validation.messsage.1.email'),
            '2.required' => __('messages.user.validation.messsage.2.required'),
            '2.in' => __('messages.user.validation.messsage.2.in'),
        ];
    }

    public function validateBulk($collection)
    {
        $i = 1;
        foreach ($collection as $col) {
            $i++;
            $errors[$i] = ['row' => $i];

            $validator = Validator::make($col->toArray(), $this->rules(), $this->validationMessages());
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $messages) {
                    foreach ($messages as $error) {
                        $errors[$i]['error'][] = $error;
                    }
                }

                $this->errors[] = (object) $errors[$i];
            }
        }

        return $this->getErrors();
    }

    public function collection(Collection $collection)
    {
        $error = $this->validateBulk($collection);

        if ($error) {
            return;
        } else {
            foreach ($collection as $col) {
                $user = User::create([
                    'name' => $col[0],
                    'email' => $col[1],
                    'gender' => $col[2],

                ]);

                $this->rows++;
            }
        }
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}

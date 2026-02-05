<?php

namespace App\Models;

use App\Traits\CreatedbyUpdatedby;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailHistory extends Model
{
    use CreatedbyUpdatedby;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['id', 'to_email', 'subject', 'body', 'created_by', 'updated_by'];

    protected $dates = ['created_at'];

    protected $hidden = [];

    protected $casts = [
        'id' => 'string',
        'to_email' => 'string',
        'body' => 'string',
        'subject' => 'string',
        'created_by' => 'string',
    ];

    public static function storeHistory($toEmails, $ccEmails, $subject, $body)
    {
        if (is_array($toEmails)) {
            $toEmails = implode(',', $toEmails);
        }

        $data['to_email'] = $toEmails;
        $data['subject'] = $subject;
        $data['body'] = $body;

        EmailHistory::create($data);
    }
}

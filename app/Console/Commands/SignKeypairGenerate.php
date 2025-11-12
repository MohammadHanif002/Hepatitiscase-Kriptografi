<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SignKeypairGenerate extends Command
{
    protected $signature = 'sign:keypair-generate';
    protected $description = 'Generate Ed25519 signing keypair for detached signatures';

    public function handle()
    {
        if (!function_exists('sodium_crypto_sign_keypair')) {
            $this->error('Ext sodium belum aktif. Aktifkan extension sodium di PHP');
            return 1; // Return a non-zero code to indicate failure
        }

        $kp = sodium_crypto_sign_keypair();
        $sk = sodium_crypto_sign_secretkey($kp);
        $pk = sodium_crypto_sign_publickey($kp);

        Storage::disk('local')->makeDirectory('keys');
        Storage::disk('local')->put('keys/sign_sk.b64', base64_encode($sk));
        Storage::disk('local')->put('keys/sign_pk.b64', base64_encode($pk));

        $this->info('OK. Tersimpan: storage/app/keys/sign_sk.b64 dan sign_pk.b64');
        $this->warn('Jangan bagikan secret key (sign_sk.b64) ke siapapun!');
        return 0; // Return zero to indicate success
    }


}

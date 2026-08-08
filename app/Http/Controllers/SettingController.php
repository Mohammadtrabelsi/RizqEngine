<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\StoreSmtpSettingsRequest;

class SettingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('access_settings'), 403);

        $settings = Setting::firstOrFail();

        return view('setting.index', compact('settings'));
    }

    public function update(StoreSettingsRequest $request)
    {
        $settings = Setting::firstOrFail();

        $data = [
            'company_name' => $request->company_name,
            'client_name' => $request->client_name,
            'company_email' => $request->company_email,
            'company_phone' => $request->company_phone,
            'notification_email' => $request->notification_email,
            'company_address' => $request->company_address,
            'default_currency_id' => $request->default_currency_id,
            'default_currency_position' => $request->default_currency_position,
        ];

        if ($request->hasFile('default_product_image')) {
            if ($settings->default_product_image) {
                Storage::disk('public')->delete($settings->default_product_image);
            }
            $data['default_product_image'] = $request->file('default_product_image')->store('settings', 'public');
        }

        if ($request->hasFile('default_category_image')) {
            if ($settings->default_category_image) {
                Storage::disk('public')->delete($settings->default_category_image);
            }
            $data['default_category_image'] = $request->file('default_category_image')->store('settings', 'public');
        }

        $settings->update($data);

        cache()->forget('settings');

        session()->flash('info', trans('setting.settings-updated'));

        return redirect()->route('settings.index');
    }

    public function updateSmtp(StoreSmtpSettingsRequest $request)
    {
        $toReplace = [
            'MAIL_MAILER='.config('mail.mailers.smtp.host'),
            'MAIL_HOST="'.config('mail.mailers.smtp.host').'"',
            'MAIL_PORT='.config('mail.mailers.smtp.port'),
            'MAIL_FROM_ADDRESS="'.config('mail.from.address').'"',
            'MAIL_FROM_NAME="'.config('mail.from.name').'"',
            'MAIL_USERNAME="'.config('mail.mailers.smtp.username').'"',
            'MAIL_PASSWORD="'.config('mail.mailers.smtp.password').'"',
            'MAIL_ENCRYPTION="'.config('mail.mailers.smtp.encryption').'"',
        ];

        $replaceWith = [
            'MAIL_MAILER='.$request->mail_mailer,
            'MAIL_HOST="'.$request->mail_host.'"',
            'MAIL_PORT='.$request->mail_port,
            'MAIL_FROM_ADDRESS="'.$request->mail_from_address.'"',
            'MAIL_FROM_NAME="'.$request->mail_from_name.'"',
            'MAIL_USERNAME="'.$request->mail_username.'"',
            'MAIL_PASSWORD="'.$request->mail_password.'"',
            'MAIL_ENCRYPTION="'.$request->mail_encryption.'"'];

        try {
            file_put_contents(base_path('.env'), str_replace($toReplace, $replaceWith, file_get_contents(base_path('.env'))));
            Artisan::call('cache:clear');

            session()->flash('info', trans('setting.smtp-settings-updated'));
        } catch (\Exception $exception) {
            Log::error($exception);
            session()->flash('error', trans('setting.smtp-settings-update-failed'));
        }

        return redirect()->route('settings.index');
    }
}

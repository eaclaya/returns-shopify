<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Libraries\EmailParser;
use App\Models\AccountCustomer;
use App\Models\AccountOrder;
use App\Models\AccountSetting;
use App\Models\EmailTemplate;
use App\Models\Request;
use Illuminate\Support\Facades\Mail;

class SendRefundRequestEmail implements ShouldQueue
{
    public $delay = 5;
    /**
     * Create the event listener.
     *
     * @return void
     */

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $request = $event->request ? $event->request : null;
        if ($request) {
            $order = AccountOrder::where('account_id', $request['account_id'])->where('order_id', $request['order_id'])->firstOrFail();
            $accountSetting = AccountSetting::with('account')->where('account_id', $order->account_id)->first();
            $customer = AccountCustomer::where('account_id', $order->account_id)->where('customer_id', $order->customer_id)->first();
            $request_id = $request['id'];
            $requestModel = Request::findOrFail($request_id);
            if ($request['transaction_type'] == 'return') {
                $requestEmailTemplate = EmailTemplate::where('account_id', $requestModel->account_id)->where('is_admin', 0)->whereIn('code', ['return'])->first();
                if($requestEmailTemplate){
                    $requestEmailTemplate->content = EmailParser::handle($requestEmailTemplate->content, $requestModel->toArray());
                    $requestEmailTemplate->subject = EmailParser::handle($requestEmailTemplate->subject, $requestModel->toArray());
                    Mail::send('emails.return', ['email' => $requestEmailTemplate], function ($mailer) use ($requestModel, $requestEmailTemplate) {
                        $mailer->from('help@mottandbow.com', 'MB Requests');
                        $mailer->to($requestModel->customer_email, $requestModel->firstname)->subject($requestEmailTemplate->subject);
                    });
                }
            }else{
                $requestEmailTemplate = EmailTemplate::where('account_id', $requestModel->account_id)->where('is_admin', 0)->whereIn('code', ['exchange', 'exchange and return'])->first();
                if($requestEmailTemplate){
                    $requestEmailTemplate->content = EmailParser::handle($requestEmailTemplate->content, $requestModel->toArray());
                    $requestEmailTemplate->subject = EmailParser::handle($requestEmailTemplate->subject, $requestModel->toArray());
                    Mail::send('emails.return', ['email' => $requestEmailTemplate], function ($mailer) use ($requestModel, $requestEmailTemplate) {
                        $mailer->from('help@mottandbow.com', 'MB Requests');
                        $mailer->to($requestModel->customer_email, $requestModel->firstname)->subject($requestEmailTemplate->subject);
                    });
                }
            }

            $requestEmailTemplate = EmailTemplate::where('account_id', $requestModel->account_id)->where('is_admin', 1)->whereIn('code', ['return', 'exchange', 'exchange and return'])->first();
            if($requestEmailTemplate){
                $requestEmailTemplate->content = EmailParser::handle($requestEmailTemplate->content, $requestModel->toArray());
                $requestEmailTemplate->subject = EmailParser::handle($requestEmailTemplate->subject, $requestModel->toArray());
                if($accountSetting && $accountSetting->notification_emails){
                    Mail::send('emails.return', ['email' => $requestEmailTemplate], function ($mailer) use ($accountSetting, $requestEmailTemplate, $order) {
                        $mailer->from('help@mottandbow.com', 'MB Requests');
                        $mailer->to(explode(',', $accountSetting->notification_emails))->subject($requestEmailTemplate->subject." - ".$order->name." | ".$order->tags);
                    });
                }
            }
        }
    }
}

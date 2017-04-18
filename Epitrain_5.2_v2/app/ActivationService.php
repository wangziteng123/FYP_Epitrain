<?php

namespace App;


use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
/**
 * ActivationService Class used for activation of user account
 */
class ActivationService
{

    protected $mailer;

    protected $activationRepo;

    protected $resendAfter = 24;
    /**
    * Construct a ActivationService object for use in sending emails to users for confirmation.
    *
    *@param array $mailer a Mailer object used to send email
    *             $activationRepo a repository containing generation and verification methods for generated activation links
    *
    * @return void
    */
    public function __construct(Mailer $mailer, ActivationRepository $activationRepo)
    {
        $this->mailer = $mailer;
        $this->activationRepo = $activationRepo;
    }
    /**
    *send email to user to activate account
    *
    *@param array $user
    *
    * @return void
    */
    public function sendActivationMail($user)
    {

        if ($user->activated || !$this->shouldSend($user)) {
            return;
        }

        $token = $this->activationRepo->createActivation($user);

        $link = route('user.activate', $token);
        $message = sprintf("Hello %s, "."\n"."Welcome to Epitrain Elearning Platform! Please activate account by clicking this link: %s". "\n"."We hope you will have an enjoyable experience! "."\n"."\n"."Regards,"."\n"."Epitrain Admin", $user->name, $link);

        $this->mailer->raw($message, function (Message $m) use ($user) {
            $m->to($user->email)->subject('Epitrain Account Activation Email');
        });

    }
    /**
    *activate user account
    *
    *@param String $token
    *
    * @return array $user
    */
    public function activateUser($token)
    {
        $activation = $this->activationRepo->getActivationByToken($token);

        if ($activation === null) {
            return null;
        }

        $user = User::find($activation->user_id);

        $user->activated = true;

        $user->save();

        $this->activationRepo->deleteActivation($token);

        return $user;

    }
    /**
    *resend activation email  after a day of account creation if user did not activate their account
    *
    *@param array $user
    *
    * @return String
    */
    private function shouldSend($user)
    {
        $activation = $this->activationRepo->getActivation($user);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }
    /**
    *  Deprecated
    *
    * @return
    */
    public function get() {
        return ['dsf','sdfsd'];
    }

}
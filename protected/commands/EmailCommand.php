<?php

/**
 * Email sender class. 
 */
class EmailCommand extends CConsoleCommand
{
    /**
     * Sends emails from the database queue. 
     */
    private function _sendEmails()
    {
        $emails = Email::model()->with('user')->findAll(
            array(
                'condition' => 'NOT sent AND attempts < :max_attempts',
                'params'    => array( 'max_attempts' => Yii::app()->params['email']['maxAttempts'] ),
                'order'     => 't.id ASC'
            )
        );
        
        foreach ($emails as $email)
        {
            $email->attempts += 1;
            
            try
            {
                $message          = new YiiMailMessage();
                $message->from    = array( Yii::app()->params['email']['systemEmail'] => Yii::app()->name );
                $message->to      = $email->user->email;
                $message->subject = $email->subject;
                $message->setBody($email->content, 'text/html', 'utf-8');
                
                Yii::app()->mail->send($message);
                
                $email->sent = true;
            }
            catch (Exception $e)
            {
                // ignore
            }
            
            $email->save();

            if ($email->sent)
                $email->delete();
        }
    }
    
    /**
     * Runs the command
     * @param array $args list of command-line arguments.
     */
    public function run($args)
    {
        // one instance check
        $fp = fopen(Yii::app()->params['email']['lockFile'], "w");
        
        if (flock($fp, LOCK_EX))
        {
            $this->_sendEmails();
            flock($fp, LOCK_UN);
        }
        
        fclose($fp);
    }
}

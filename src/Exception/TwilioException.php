<?php

namespace TwilioSMS\Exception;

use Exception;

class TwilioException extends Exception
{
    private ?string $twilioCode = null;
    
    public function setTwilioCode(?string $code): self
    {
        $this->twilioCode = $code;
        return $this;
    }
    
    public function getTwilioCode(): ?string
    {
        return $this->twilioCode;
    }
}

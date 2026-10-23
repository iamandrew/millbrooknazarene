<?php

namespace Application\Block\ExpressForm;

use Application\Form\SilentSpamGuard;
use Concrete\Block\ExpressForm\Controller as CoreController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Support\Facade\Url;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends CoreController
{
    public function view()
    {
        parent::view();

        $guard = $this->app->make(SilentSpamGuard::class);
        $timing = $guard->issueTimingFields($this->formGuardKey());
        $this->set('millbrookSpamGuard', $guard);
        $this->set('millbrookFormStarted', $timing['started']);
        $this->set('millbrookFormSignature', $timing['signature']);
    }

    public function action_submit($bID = null)
    {
        if ((string) $this->bID !== (string) $bID) {
            return parent::action_submit($bID);
        }

        $guard = $this->app->make(SilentSpamGuard::class);
        $outcome = $guard->inspect($this->request, $this->formGuardKey());

        if ($outcome === SilentSpamGuard::OUTCOME_SILENT) {
            return $this->fakeSuccessRedirect();
        }

        if ($outcome === SilentSpamGuard::OUTCOME_RATE_LIMITED) {
            $error = new ErrorList();
            $error->add(t('Thanks — please wait a few minutes before sending another message.'));
            $this->set('error', $error);
            $this->view();

            return false;
        }

        return parent::action_submit($bID);
    }

    private function formGuardKey(): string
    {
        return 'express_form_' . $this->bID;
    }

    private function fakeSuccessRedirect()
    {
        $url = Url::to($this->request->getCurrentPage(), 'form_success', $this->bID);
        $redirect = Redirect::to($url);
        $redirect->setTargetUrl($redirect->getTargetUrl() . '#form' . $this->bID);

        return $redirect;
    }
}

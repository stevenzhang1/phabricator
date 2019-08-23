<?php

final class PhortuneAccountEmailViewController
  extends PhortuneAccountController {

  protected function shouldRequireAccountEditCapability() {
    return true;
  }

  protected function handleAccountRequest(AphrontRequest $request) {
    $viewer = $this->getViewer();
    $account = $this->getAccount();

    $address = id(new PhortuneAccountEmailQuery())
      ->setViewer($viewer)
      ->withAccountPHIDs(array($account->getPHID()))
      ->withIDs(array($request->getURIData('id')))
      ->executeOne();
    if (!$address) {
      return new Aphront404Response();
    }

    $crumbs = $this->buildApplicationCrumbs()
      ->addTextCrumb(pht('Email Addresses'), $account->getEmailAddressesURI())
      ->addTextCrumb($address->getObjectName())
      ->setBorder(true);

    $header = id(new PHUIHeaderView())
      ->setHeader(pht('Account Email: %s', $address->getAddress()));

    $details = $this->newDetailsView($address);

    $timeline = $this->buildTransactionTimeline(
      $address,
      new PhortuneAccountEmailTransactionQuery());
    $timeline->setShouldTerminate(true);

    $curtain = $this->buildCurtainView($address);

    $view = id(new PHUITwoColumnView())
      ->setHeader($header)
      ->setCurtain($curtain)
      ->setMainColumn(
        array(
          $details,
          $timeline,
        ));

    return $this->newPage()
      ->setTitle($address->getObjectName())
      ->setCrumbs($crumbs)
      ->appendChild($view);
  }

  private function buildCurtainView(PhortuneAccountEmail $address) {
    $viewer = $this->getViewer();
    $account = $address->getAccount();

    $can_edit = PhabricatorPolicyFilter::hasCapability(
      $viewer,
      $address,
      PhabricatorPolicyCapability::CAN_EDIT);

    $edit_uri = $this->getApplicationURI(
      urisprintf(
        'account/%d/addresses/edit/%d/',
        $account->getID(),
        $address->getID()));

    $curtain = $this->newCurtainView($account);

    $curtain->addAction(
      id(new PhabricatorActionView())
        ->setName(pht('Edit Address'))
        ->setIcon('fa-pencil')
        ->setHref($edit_uri)
        ->setDisabled(!$can_edit)
        ->setWorkflow(!$can_edit));

    return $curtain;
  }

  private function newDetailsView(PhortuneAccountEmail $address) {
    $viewer = $this->getViewer();

    $view = id(new PHUIPropertyListView())
      ->setUser($viewer);

    $view->addProperty(pht('Email Address'), $address->getAddress());

    return id(new PHUIObjectBoxView())
      ->setHeaderText(pht('Email Address Details'))
      ->setBackground(PHUIObjectBoxView::BLUE_PROPERTY)
      ->addPropertyList($view);
  }
}
<?php

namespace Mailery\Sender\Domain\Controller;

use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Form\DomainForm;
use Mailery\Sender\Domain\Service\DomainCrudService;
use Mailery\Sender\Domain\Service\DnsCheckerService;
use Mailery\Brand\BrandLocatorInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Http\Header;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Mailery\Sender\Domain\Repository\DomainRepository;
use Mailery\Sender\Domain\ValueObject\DomainValueObject;

class SettingsController
{
    /**
     * @var ViewRenderer
     */
    private ViewRenderer $viewRenderer;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var UrlGenerator
     */
    private UrlGenerator $urlGenerator;

    /**
     * @var DomainRepository
     */
    private DomainRepository $domainRepo;

    /**
     * @var DomainCrudService
     */
    private DomainCrudService $domainCrudService;

    /**
     * @var BrandLocatorInterface
     */
    private BrandLocatorInterface $brandLocator;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param UrlGenerator $urlGenerator
     * @param DomainCrudService $domainCrudService
     * @param BrandLocatorInterface $brandLocator
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        UrlGenerator $urlGenerator,
        DomainRepository $domainRepo,
        DomainCrudService $domainCrudService,
        BrandLocatorInterface $brandLocator
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewBasePath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->domainRepo = $domainRepo->withBrand($brandLocator->getBrand());
        $this->domainCrudService = $domainCrudService;
        $this->brandLocator = $brandLocator;
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param DomainForm $form
     * @return Response
     */
    public function domain(Request $request, ValidatorInterface $validator, FlashInterface $flash, DomainForm $form): Response
    {
        $body = $request->getParsedBody();

        if (($domain = $this->getDomain()) !== null) {
            $form = $form->withDomain($domain);
        }

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form, $form->getRules())) {
            $valueObject = DomainValueObject::fromForm($form)
                ->withBrand($this->brandLocator->getBrand());

            if ($domain !== null) {
                if (empty($valueObject->getDomain())) {
                    $this->domainCrudService->delete($domain);
                    $domain = null;
                } else {
                    $this->domainCrudService->update($domain, $valueObject);
                }
            } else {
                $domain = $this->domainCrudService->create($valueObject);
            }

            $flash->add(
                'success',
                [
                    'body' => 'Settings have been saved!',
                ],
                true
            );
        }

        return $this->viewRenderer->render('domain', compact('form', 'domain'));
    }

    /**
     * @param DnsCheckerService $dnsChecker
     * @return Response
     */
    public function checkDns(DnsCheckerService $dnsChecker): Response
    {
        if (($domain = $this->getDomain()) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $dnsChecker->checkAll($domain->getDnsRecords());

        return $this->responseFactory
            ->createResponse(Status::FOUND)
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/brand/settings/domain'));
    }

    /**
     * @return Domain|null
     */
    private function getDomain(): ?Domain
    {
        return $this->domainRepo->findOne();
    }
}

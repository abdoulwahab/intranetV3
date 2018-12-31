<?php

namespace App\EventSubscriber;

use App\Entity\Notification;
use App\Entity\StageEtudiant;
use App\Entity\StageMailTemplate;
use App\Repository\StageMailTemplateRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Events;
use App\MesClasses\Mail\MyMailer;
use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class StageSubscriber implements EventSubscriberInterface
{

    /** @var MyMailer */
    protected $myMailer;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RouterInterface */
    private $router;

    /** @var StageMailTemplateRepository */
    private $stageMailTemplateRepository;

    /**
     * StageSubscriber constructor.
     *
     * @param EntityManagerInterface      $entityManager
     * @param RouterInterface             $router
     * @param MyMailer                    $myMailer
     * @param StageMailTemplateRepository $stageMailTemplateRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        MyMailer $myMailer,
        StageMailTemplateRepository $stageMailTemplateRepository
    ) {
        $this->entityManager = $entityManager;
        $this->stageMailTemplateRepository = $stageMailTemplateRepository;
        $this->router = $router;
        $this->myMailer = $myMailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::MAIL_CHGT_ETAT_STAGE_AUTORISE           => 'onMailChgtEtatStageAutorise',
            Events::MAIL_CHGT_ETAT_STAGE_DEPOSE             => 'onMailChgtEtatStageDepose',
            Events::MAIL_CHGT_ETAT_STAGE_VALIDE             => 'onMailChgtEtatStageValide',
            Events::MAIL_CHGT_ETAT_STAGE_IMPRIME            => 'onMailChgtEtatStageImprime',
            Events::MAIL_CHGT_ETAT_STAGE_CONVENTION_ENVOYEE => 'onMailChgtEtatStageConventionEnvoyee',
            Events::MAIL_CHGT_ETAT_CONVENTION_RECUE         => 'onMailChgtEtatStageConventionRecue',

            Events::CHGT_ETAT_STAGE_AUTORISE           => 'onChgtEtatStageAutorise',
            Events::CHGT_ETAT_STAGE_DEPOSE             => 'onChgtEtatStageDepose',
            Events::CHGT_ETAT_STAGE_VALIDE             => 'onChgtEtatStageValide',
            Events::CHGT_ETAT_STAGE_IMPRIME            => 'onChgtEtatStageImprime',
            Events::CHGT_ETAT_STAGE_CONVENTION_ENVOYEE => 'onChgtEtatStageConventionEnvoyee',
            Events::CHGT_ETAT_CONVENTION_RECUE         => 'onChgtEtatStageConventionRecue',
        ];
    }

    public function onChgtEtatStageAutorise(GenericEvent $event): void
    {
        $this->addNotification($event, Events::CHGT_ETAT_STAGE_AUTORISE);
    }

    private function addNotification(GenericEvent $event, $codeEvent): void
    {
        /** @var StageEtudiant $stageEtudiant */
        $stageEtudiant = $event->getSubject();
        if ($stageEtudiant->getEtudiant() !== null) {
            $notif = new Notification();
            $notif->setEtudiant($stageEtudiant->getEtudiant());
            $notif->setTypeUser(Notification::ETUDIANT);
            $notif->setType($codeEvent);
            $notif->setUrl($this->router->generate(
                'user_mon_profil',
                ['onglet' => 'absence']
            ));
            //todo: lien vers detail du stage
            $this->entityManager->persist($notif);
            $this->entityManager->flush();
        }
    }

    public function onChgtEtatStageConventionEnvoyee(GenericEvent $event): void
    {
        $this->addNotification($event, Events::CHGT_ETAT_STAGE_CONVENTION_ENVOYEE);
    }

    public function onChgtEtatStageConventionRecue(GenericEvent $event): void
    {
        $this->addNotification($event, Events::CHGT_ETAT_CONVENTION_RECUE);
    }

    public function onChgtEtatStageDepose(GenericEvent $event): void
    {
        /** @var StageEtudiant $stageEtudiant */
        $stageEtudiant = $event->getSubject();
        if ($stageEtudiant->getStagePeriode() !== null) {
            foreach ($stageEtudiant->getStagePeriode()->getResponsables() as $personnel) {
                $notif = new Notification();
                $notif->setPersonnel($personnel);
                $notif->setTypeUser(Notification::PERSONNEL);
                $notif->setType(Events::CHGT_ETAT_STAGE_DEPOSE);
                $notif->setUrl($this->router->generate(
                    'user_mon_profil',
                    ['onglet' => 'absence']
                ));
                //todo: lien vers detail du stage
                $this->entityManager->persist($notif);
            }
            $this->entityManager->flush();
        }

        $this->addNotification($event, Events::CHGT_ETAT_STAGE_DEPOSE);
    }

    public function onChgtEtatStageValide(GenericEvent $event): void
    {
        $this->addNotification($event, Events::CHGT_ETAT_STAGE_VALIDE);
    }

    public function onChgtEtatStageImprime(GenericEvent $event): void
    {
        $this->addNotification($event, Events::CHGT_ETAT_STAGE_IMPRIME);
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onMailChgtEtatStageAutorise(GenericEvent $event): void
    {
        $this->sendMail($event, Events::MAIL_CHGT_ETAT_STAGE_AUTORISE);
    }

    /**
     * @param GenericEvent $event
     * @param              $codeEvent
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function sendMail(GenericEvent $event, $codeEvent): void
    {
        /** @var StageEtudiant $stageEtudiant */
        $stageEtudiant = $event->getSubject();

        //table avec les templates des mails et le sujet, a récupérer en fonction du codeEvent et de la période.
        /** @var StageMailTemplate $mailTemplate */
        $mailTemplate = $this->stageMailTemplateRepository->findEventPeriode(
            $codeEvent,
            $stageEtudiant->getStagePeriode()
        );

        if ($mailTemplate !== null && $mailTemplate->getTwigTemplate() !== null && $stageEtudiant->getEtudiant() !== null) {
            $this->myMailer->setTemplateFromDatabase($mailTemplate->getTwigTemplate()->getName(),
                ['stageEtudiant' => $stageEtudiant]);
            $this->myMailer->sendMessage($stageEtudiant->getEtudiant()->getMails(), $mailTemplate->getSubject());
        } else {
            //mail par défaut
            //todo: traiter le sujet avec la traduction ?

            $this->myMailer->setTemplate('mails/stage_'.$codeEvent.'.txt.twig',
                ['stageEtudiant' => $stageEtudiant]);
            $this->myMailer->sendMessage($stageEtudiant->getEtudiant()->getMails(), $codeEvent);

        }

        //mail en copie à l'assistante
        if ($stageEtudiant->getStagePeriode() !== null && $stageEtudiant->getStagePeriode()->getCopieAssistant() && $stageEtudiant->getStagePeriode()->getMailAssistant() !== null) {
            //selon un modèle spécifique
            $mailTemplate = $this->stageMailTemplateRepository->findEventPeriode(
                $codeEvent . '_COPIE',
                $stageEtudiant->getStagePeriode()
            );

            if ($mailTemplate !== null && $mailTemplate->getTwigTemplate() && $stageEtudiant->getEtudiant() !== null) {
                $this->myMailer->setTemplateFromDatabase($mailTemplate->getTwigTemplate()->getName(),
                    ['stageEtudiant' => $stageEtudiant]);
                $this->myMailer->sendMessage(
                    $stageEtudiant->getStagePeriode()->getMailAssistant(),
                    $mailTemplate->getSubject()
                );
            } else {
                //sinon mail par défaut
                //todo: traiter le sujet avec la traduction ?
                    $this->myMailer->setTemplate('mails/stage_assistant_' . $codeEvent . '.txt.twig',
                        ['stageEtudiant' => $stageEtudiant]);
                    $this->myMailer->sendMessage($stageEtudiant->getStagePeriode()->getMailAssistant(),
                        'copie ' . $codeEvent);
                }
        }

        //copie au RP lors du dépôt par l'étudiant
        if ($codeEvent === Events::MAIL_CHGT_ETAT_STAGE_DEPOSE) {
            $this->myMailer->setTemplate('mails/stage_copie_depot_rp.txt.twig',
                ['stageEtudiant' => $stageEtudiant]);
            $tMails = [];
            foreach ($stageEtudiant->getStagePeriode()->getResponsables() as $resp) {
                $tMails[] = $resp->getMailUniv();
            }
            $this->myMailer->sendMessage($tMails, $mailTemplate->getSubject());
        }
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onMailChgtEtatStageConventionEnvoyee(GenericEvent $event): void
    {
        $this->sendMail($event, Events::MAIL_CHGT_ETAT_STAGE_CONVENTION_ENVOYEE);
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onMailChgtEtatStageConventionRecue(GenericEvent $event): void
    {
        $this->sendMail($event, Events::MAIL_CHGT_ETAT_CONVENTION_RECUE);
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     */
    public function onMailChgtEtatStageDepose(GenericEvent $event): void
    {
        //todo: ajouter un mail au RP de stage
        $this->sendMail($event, Events::MAIL_CHGT_ETAT_STAGE_DEPOSE);
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onMailChgtEtatStageValide(GenericEvent $event): void
    {
        $this->sendMail($event, Events::MAIL_CHGT_ETAT_STAGE_VALIDE);
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onMailChgtEtatStageImprime(GenericEvent $event): void
    {
        $this->sendMail($event, Events::MAIL_CHGT_ETAT_STAGE_IMPRIME);
    }
}

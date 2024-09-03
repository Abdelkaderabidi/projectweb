<?php

namespace App\Service;
use App\Entity\Produit;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    private $mailer;

    // Injectez le service de mailing lors de l'initialisation
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(Produit $produit)
    {
        // Calcul du prix après la promotion
        $prixApresPromo = $produit->getPrix() - ($produit->getPrix() * ($produit->getPrixApresPromo() / 100));

        $email = (new Email())
            ->from('mailtrap@demomailtrap.com')
            ->to('saif.meddeb.100@gmail.com')
            ->subject('Nouvelle promotion sur ' . $produit->getNomProd())
            ->html(sprintf(
                '<p>Le produit %s est en promotion du %s au %s et son nouveau prix est de %s DT. Allez maintenant visiter notre boutique!!</p>',
                $produit->getNomProd(),
                $produit->getDateDebutPromo()->format('Y-m-d'),
                $produit->getDateFinPromo()->format('Y-m-d'),
                $prixApresPromo
            ));

        // Assurez-vous que $this->mailer n'est pas null avant d'appeler send()
        if ($this->mailer) {
            $this->mailer->send($email);
        } else {
            // Gérer le cas où le mailer est null
            // Vous pouvez lever une exception ou loguer un message d'erreur par exemple
        }
    }
}
<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;


class QuackVoter extends Voter
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EDIT', 'DELETE'])
            && $subject instanceof \App\Entity\Quack;
    }

    protected function voteOnAttribute($attribute, $quack, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        if(null == $quack->getAuthor()) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        switch ($attribute) {
            case 'EDIT':
                return $quack->getAuthor()->getId() == $user->getId();
                // return true or false
                break;
            case 'DELETE':
                return $quack->getAuthor()->getId() == $user->getId();
                // return true or false
                break;
            case 'CREATE':
                return $quack->getAuthor()->getId() == $user->getId();
                // return true or false
                break;
        }

        return false;
    }
}

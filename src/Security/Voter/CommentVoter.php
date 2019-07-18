<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EDIT', 'COMMENT_DELETE'])
            && $subject instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute($attribute, $comment, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if(null == $comment->getAuthor()) {
            return false;
        }
        switch ($attribute) {
            case 'EDIT':
                return $comment->getAuthor()->getId() == $user->getId();
                // return true or false
                break;
            case 'COMMENT_DELETE':
                return $comment->getAuthor()->getId() == $user->getId() || $comment->getQuack()->getAuthor()->getId() == $user->getId();
                // return true or false
                break;
            case 'CREATE':
                return $comment->getAuthor()->getId() == $user->getId();
                // return true or false
                break;
        }

        return false;
    }
}

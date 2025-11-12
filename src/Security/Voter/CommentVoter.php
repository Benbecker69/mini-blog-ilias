<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
    public const DELETE = 'COMMENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Only vote on Comment objects for DELETE attribute
        return $attribute === self::DELETE && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Comment $comment */
        $comment = $subject;

        return match($attribute) {
            self::DELETE => $this->canDelete($comment, $user),
            default => false,
        };
    }

    private function canDelete(Comment $comment, User $user): bool
    {
        // Admins can delete any comment
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Authors can delete their own comments
        return $comment->getAuthor() === $user;
    }
}

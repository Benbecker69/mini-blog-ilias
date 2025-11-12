<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    public const EDIT = 'ARTICLE_EDIT';
    public const DELETE = 'ARTICLE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Only vote on Article objects for EDIT and DELETE attributes
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Article $article */
        $article = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($article, $user),
            self::DELETE => $this->canDelete($article, $user),
            default => false,
        };
    }

    private function canEdit(Article $article, User $user): bool
    {
        // Admins can edit any article
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Authors can edit their own articles
        return $article->getAuthor() === $user;
    }

    private function canDelete(Article $article, User $user): bool
    {
        // Admins can delete any article
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Authors can delete their own articles
        return $article->getAuthor() === $user;
    }
}

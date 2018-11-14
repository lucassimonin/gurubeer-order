<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 28/01/2018
 * Time: 23:02
 */

namespace App\Security;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class OrderVoter
 * @package App\Security
 */
class OrderVoter extends Voter
{
    const ORDER_DELETE = 'order-delete';

    private $decisionManager;

    /**
     * UserVoter constructor.
     *
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::ORDER_DELETE))) {
            return false;
        }

        if ($subject instanceof Order) {
            return true;
        }

        return false;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array(User::ROLE_SUPER_ADMIN))) {
            return true;
        }
        $order = $subject;
        /** @var User $user */
        $user = $token->getUser();

        switch ($attribute) {
            case self::ORDER_DELETE:
                return $this->canDelete($order, $user);
            default:
                return true;
        }
    }

    public function canDelete(Order $order, User $user)
    {
        return $order->getCreator()->getId() === $user->getId();
    }
}

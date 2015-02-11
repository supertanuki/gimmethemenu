<?php
namespace Application\MainBundle\Provider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    /**
     * Connect user
     *
     * @param UserInterface $user
     * @param UserResponseInterface $response
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        // we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        // we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * Load User By OAuth
     *
     * @param UserResponseInterface $response
     * @return \FOS\UserBundle\Model\UserInterface|UserInterface
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $email = $response->getEmail();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        if (null === $user) {

            if ($email) {
                $user = $this->userManager->findUserByEmail($email);
            }

            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';

            if (null === $user || !$user instanceof UserInterface) {

                // create new user here
                $user = $this->userManager->createUser();
                $user->$setter_id($username);
                $user->$setter_token($response->getAccessToken());

                $user->setUsername($username);
                $user->setEmail($email ? $email : $username);
                $user->setFirstName($response->getRealName() ? $response->getRealName() : $response->getNickname());
                $user->setPassword('');
                $user->setEnabled(true);
                $this->userManager->updateUser($user);
                return $user;

            } else {

                // update the user
                $user->$setter_id($username);
                $user->$setter_token($response->getAccessToken());
                $this->userManager->updateUser($user);

                return $user;
            }
        }

        // if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        // update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

}
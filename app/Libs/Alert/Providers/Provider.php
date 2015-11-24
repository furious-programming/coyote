<?php

namespace Coyote\Alert\Providers;

use Coyote\Alert\Emitters\Db as Db_Emitter;
use Coyote\Alert\Emitters\Email as Email_Emitter;
use Coyote\Repositories\Contracts\AlertRepositoryInterface;

/**
 * Class Provider
 * @package Coyote\Alert\Providers
 */
abstract class Provider implements ProviderInterface
{
    /**
     * @var AlertRepositoryInterface
     */
    protected $repository;

    /**
     * @var array
     */
    protected $usersId = [];

    /**
     * @var int
     */
    protected $typeId;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $excerpt;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $senderId;

    /**
     * @var string
     */
    protected $senderName;

    /**
     * @var string
     */
    protected $headline;

    /**
     * @param AlertRepositoryInterface $repository
     * @param array $args
     * @throws \Exception
     */
    public function __construct(AlertRepositoryInterface $repository, array $args = [])
    {
        $this->repository = $repository;
        $this->typeId = static::ID;

        $this->headline = $this->repository->headlinePattern($this->typeId);
        if (!$this->headline) {
            throw new \Exception('Uuups. Could not find record in alert_types table.');
        }

        if ($args) {
            foreach ($args as $arg => $value) {
                $this->{'set' . camel_case($arg)}($value);
            }
        }
    }

    /**
     * Typ ID powiadomienia
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function addUserId($userId)
    {
        $this->usersId[] = $userId;
        return $this;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function setUserId($userId)
    {
        $this->usersId = [$userId];
        return $this;
    }

    /**
     * @param array $usersId
     * @return mixed
     */
    public function setUsersId(array $usersId)
    {
        $this->usersId = $usersId;
        return $this;
    }

    /**
     * @return array
     */
    public function getUsersId()
    {
        return $this->usersId;
    }

    /**
     * Tytul powiadomienia - np. tytul watku na forum czy nazwa oferty pracy
     *
     * @param string $subject
     * @return mixed
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Krótka zajawka powiadomienia (np. pierwsze kilkadziesiat znakow wpisu czy komentarza)
     *
     * @param string $excerpt
     * @return mixed
     */
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    /**
     * @return string
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }

    /**
     * @param string $headline
     * @return mixed
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * URL do powiadonienie (najlepiej nierelatywny)
     *
     * @param string $url
     * @return mixed
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * ID usera ktory generuje powiadomienie (np. autora posta na forum)
     *
     * @param int $senderId
     * @return mixed
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * Nazwa uzytkownika ktory geneuje powiadomienia. Moze to byc login uzytkownika albo nick podany
     * na forum jezeli uzytkownik nie jest zalogowany
     *
     * @param $senderName
     * @return mixed
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->getSenderName();
    }

    /**
     * Unikalne ID okreslajace dano powiadomienie. To ID posluzy do grupowania powiadomien tego samego typu
     *
     * @return string
     */
    public function objectId()
    {
        return substr(md5($this->typeId . $this->subject), 16);
    }

    /**
     * Zwraca nazwe szablonu e-mail dla danego alertu
     *
     * @return string
     */
    public function email()
    {
        return static::EMAIL;
    }

    /**
     * Generuje powiadomienie oraz zwraca ID userow do ktorych zostalo wyslane
     *
     * @return array
     */
    public function notify()
    {
        // pobranie ustawien powiadomienia dla userow. byc moze maja oni wylaczone powiadomienie tego typu?
        $users = $this->repository->userSettings($this->typeId, $this->getUsersId());
        $recipients = [];

        foreach ($users as $user) {
            // wysylamy powiadomienie ktore bedzie widoczne w profilu uzytkownika
            if ($user['profile']) {
                $notifier = new Db_Emitter($this->repository, $user['user_id']);
                $notifier->send($this);

                $recipients[] = $user['user_id'];
            }

            if ($user['email'] && $this->email() && $user['user_email'] && $user['is_active']
                && $user['is_confirm'] && !$user['is_blocked']) {
                $notifier = new Email_Emitter($this->email(), $user['user_email']);
                $notifier->send($this);

                $recipients[] = $user['user_id'];
            }
        }

        return array_unique($recipients);
    }
}

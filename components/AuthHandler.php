<?php

namespace fat2fast\auth\components;

use fat2fast\auth\models\User;
use fat2fast\auth\models\UserAuth;
use fat2fast\auth\models\UserProfile;
use Yii;
use yii\authclient\ClientInterface;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * UserAuthHandler handles successful UserAuthentication via Yii UserAuth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function handle()
    {

        $attributes = $this->client->getUserAttributes();
        if ($this->client->getId() == 'vnconnect') {
            $email = ArrayHelper::getValue($attributes, 'SoDinhDanh');
            $id = ArrayHelper::getValue($attributes, 'sub');
            $nickname = ArrayHelper::getValue($attributes, 'HoVaTen');
        } else {
            $email = ArrayHelper::getValue($attributes, 'email');
            $id = ArrayHelper::getValue($attributes, 'id');
            $nickname = ArrayHelper::getValue($attributes, 'name');
        }

        // vnconnect array(9) {
        // ["userProfileUri"]=> string(1674) "https://sso.dancuquocgia.gov.vn/api/idp/third-party/view-info?token_id=eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJRUDcwRHlzdV9UZTZNYW1Pa29Ud0dHS1VLTjczb1RBX0dGT2gxckd1dWVFIn0.eyJleHAiOjE3MDIzMjA3NTQsImlhdCI6MTcwMjMxODk1NCwianRpIjoiZGExZjk5YmUtZTA5OC00NDRlLWEyOGMtYTQyMGMwODFjYTQ0IiwiaXNzIjoiaHR0cDovL3Nzby5kYW5jdXF1b2NnaWEuZ292LnZuOjgwODAvYXV0aC9yZWFsbXMvY2l0aXplbiIsImF1ZCI6ImFjY291bnQiLCJzdWIiOiJlMjI2YmE2Yy03NjM5LTRhMDAtODliNC03YTI1MDc0YzY2YzEiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJ2bmNvbm5lY3QiLCJzZXNzaW9uX3N0YXRlIjoiZDMwNDQ5M2EtY2YzZi00YzI4LWFkNDUtNDBmNGY5Yzg0YzVlIiwiYWNyIjoiMSIsImFsbG93ZWQtb3JpZ2lucyI6WyJodHRwczovL3Rlc3R4YWN0aHVjLmRpY2h2dWNvbmcuZ292LnZuIiwiaHR0cHM6Ly94YWN0aHVjLmRpY2h2dWNvbmcuZ292LnZuL2NvbW1vbmF1dGgiLCJodHRwOi8vbG9jYWxob3N0OjgwODAiLCJodHRwczovL3Rlc3QuZGljaHZ1Y29uZy5nb3Yudm4iXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbImRlZmF1bHQtcm9sZXMtY2l0aXplbiIsIm9mZmxpbmVfYWNjZXNzIiwidW1hX2F1dGhvcml6YXRpb24iXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6Im9wZW5pZCBlbWFpbCBwcm9maWxlIiwic2lkIjoiZDMwNDQ5M2EtY2YzZi00YzI4LWFkNDUtNDBmNGY5Yzg0YzVlIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJuYW1lIjoiTWFuZyBWacOqbiBUw7luZyIsInByZWZlcnJlZF91c2VybmFtZSI6IjA1MjA4ODAwMDI4MCIsImdpdmVuX25hbWUiOiJNYW5nIiwiZmFtaWx5X25hbWUiOiJWacOqbiBUw7luZyJ9.FRo2YLTGmKWMXP2IFyj_3R8rcfGdc8YeqUR01133_g4tG2yhVPWprq62jIXfMZryOMoQ1naPqC0-X-3HFjaw3P7p0Ci2l-oXf0-4p1CUImXaXW3nX0z4_OrWBTqlByb_0NtT26aejrKoYnad6FPjk5Gn2vAVTfIOSC4rlYCyQMmuqVAlRdgaNOgrvxn2-agJQrzYmZ0UQmNc85EUO7ojnr6YMuTpGo0XWu1qeG1BK7CbGNgmcuj5cABf5oAtbUCsa9zfAyubHt6a-T__4AQLiWrPiy2ihOPk0oz3KCG73s2PRprC522vvgkkVhbi2T2KjTrILNkLfWhlRgyKMGzBAA" ["logoutUri"]=> string(1671) "https://sso.dancuquocgia.gov.vn/api/idp/third-party/logout?token_id=eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJRUDcwRHlzdV9UZTZNYW1Pa29Ud0dHS1VLTjczb1RBX0dGT2gxckd1dWVFIn0.eyJleHAiOjE3MDIzMjA3NTQsImlhdCI6MTcwMjMxODk1NCwianRpIjoiZGExZjk5YmUtZTA5OC00NDRlLWEyOGMtYTQyMGMwODFjYTQ0IiwiaXNzIjoiaHR0cDovL3Nzby5kYW5jdXF1b2NnaWEuZ292LnZuOjgwODAvYXV0aC9yZWFsbXMvY2l0aXplbiIsImF1ZCI6ImFjY291bnQiLCJzdWIiOiJlMjI2YmE2Yy03NjM5LTRhMDAtODliNC03YTI1MDc0YzY2YzEiLCJ0eXAiOiJCZWFyZXIiLCJhenAiOiJ2bmNvbm5lY3QiLCJzZXNzaW9uX3N0YXRlIjoiZDMwNDQ5M2EtY2YzZi00YzI4LWFkNDUtNDBmNGY5Yzg0YzVlIiwiYWNyIjoiMSIsImFsbG93ZWQtb3JpZ2lucyI6WyJodHRwczovL3Rlc3R4YWN0aHVjLmRpY2h2dWNvbmcuZ292LnZuIiwiaHR0cHM6Ly94YWN0aHVjLmRpY2h2dWNvbmcuZ292LnZuL2NvbW1vbmF1dGgiLCJodHRwOi8vbG9jYWxob3N0OjgwODAiLCJodHRwczovL3Rlc3QuZGljaHZ1Y29uZy5nb3Yudm4iXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbImRlZmF1bHQtcm9sZXMtY2l0aXplbiIsIm9mZmxpbmVfYWNjZXNzIiwidW1hX2F1dGhvcml6YXRpb24iXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6Im9wZW5pZCBlbWFpbCBwcm9maWxlIiwic2lkIjoiZDMwNDQ5M2EtY2YzZi00YzI4LWFkNDUtNDBmNGY5Yzg0YzVlIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJuYW1lIjoiTWFuZyBWacOqbiBUw7luZyIsInByZWZlcnJlZF91c2VybmFtZSI6IjA1MjA4ODAwMDI4MCIsImdpdmVuX25hbWUiOiJNYW5nIiwiZmFtaWx5X25hbWUiOiJWacOqbiBUw7luZyJ9.FRo2YLTGmKWMXP2IFyj_3R8rcfGdc8YeqUR01133_g4tG2yhVPWprq62jIXfMZryOMoQ1naPqC0-X-3HFjaw3P7p0Ci2l-oXf0-4p1CUImXaXW3nX0z4_OrWBTqlByb_0NtT26aejrKoYnad6FPjk5Gn2vAVTfIOSC4rlYCyQMmuqVAlRdgaNOgrvxn2-agJQrzYmZ0UQmNc85EUO7ojnr6YMuTpGo0XWu1qeG1BK7CbGNgmcuj5cABf5oAtbUCsa9zfAyubHt6a-T__4AQLiWrPiy2ihOPk0oz3KCG73s2PRprC522vvgkkVhbi2T2KjTrILNkLfWhlRgyKMGzBAA"
        // ["sub"]=> string(36) "183f087e-a5d1-4705
        // ["TechID"]=> string(36) "183f087e-a5d1"
        // ["LoaiTaiKhoan"]=> string(1) "1"
        // ["HoVaTen"]=> string(16) "Nguyen Van A"
        // ["SoDinhDanh"]=> string(12) "052088000281"
        // ["loginIdp"]=> string(3) "BCA"
        // ["NgayThangNamSinh"]=> string(8) "19880219"
        // }
        // google: { ["id"]=> string(21) "101595025227503615123" ["email"]=> string(17) "tungmv7@gmail.com" ["verified_email"]=> bool(true) ["name"]=> string(9) "Tung Mang" ["given_name"]=> string(4) "Tung" ["family_name"]=> string(4) "Mang" ["picture"]=> string(94) "https://lh3.googleusercontent.com/a/ACg8ocKYdenblV75T3MVp7qgKdawnsyMpjdlEG_hSbu9NDIRZB2P=s96-c" ["locale"]=> string(2) "en" }

        /* @var UserAuth $auth */
        $auth = UserAuth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                /* @var User $user */
                $user = $auth->user;
                $this->updateUserInfo($user);
                $this->login($auth->user_id);
            } else { // signup
                if ($email !== null && User::find()->where(['email' => $email])->exists()) {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('yii2-authz', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $this->client->getTitle()]),
                    ]);
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
                    $user = new User([
                        'username' => $email,
                        'email' => $email,
                        'password' => $password,
                        'status' => User::STATUS_ACTIVE // make sure you set status properly
                    ]);
                    $user->generateAuthKey();
                    $user->generatePasswordResetToken();

                    // $transaction = Yii::$app->db->beginTransaction();

                    if ($user->save()) {
                        $auth = new UserAuth([
                            'user_id' => $user->id,
                            'source' => $this->client->getId(),
                            'source_id' => (string) $id,
                            'payload' => json_encode($this->client->getUserAttributes())
                        ]);
                        if ($auth->save()) {
                            $this->updateUserInfo($user);
                            $this->login($auth->user_id);
                        } else {
                            Yii::$app->getSession()->setFlash('error', [
                                Yii::t('yii2-authz', 'Unable to save {client} account: {errors}', [
                                    'client' => $this->client->getTitle(),
                                    'errors' => json_encode($auth->getErrors()),
                                ]),
                            ]);
                        }
                    } else {
                        Yii::$app->getSession()->setFlash('error', [
                            Yii::t('yii2-authz', 'Unable to save user: {errors}', [
                                'client' => $this->client->getTitle(),
                                'errors' => json_encode($user->getErrors()),
                            ]),
                        ]);
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add UserAuth provider
                $auth = new UserAuth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $this->client->getId(),
                    'source_id' => (string)$attributes['id'],
                ]);
                if ($auth->save()) {
                    /** @var User $user */
                    $user = $auth->user;
                    $this->updateUserInfo($user);
                    $this->login($auth->user_id);

                    Yii::$app->getSession()->setFlash('success', [
                        Yii::t('yii2-authz', 'Linked {client} account.', [
                            'client' => $this->client->getTitle()
                        ]),
                    ]);
                } else {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('yii2-authz', 'Unable to link {client} account: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($auth->getErrors()),
                        ]),
                    ]);
                }
            } else { // there's existing UserAuth
                Yii::$app->getSession()->setFlash('error', [
                    Yii::t('yii2-authz',
                        'Unable to link {client} account. There is another user using it.',
                        ['client' => $this->client->getTitle()]),
                ]);
            }
        }
    }

    /**
     * @param $user
     */
    private function updateUserInfo($user)
    {
        $attributes = $this->client->getUserAttributes();
        // check has user profile

        // create user profile if it doesn't exist
        if ($user->userProfile) {
            // update
            $us = $user->userProfile;
        } else {
            $us = new UserProfile();
        }

        $attrs = [
            'user_id' => $user->id,
            'first_name' => ArrayHelper::getValue($attributes, 'family_name', ' '),
            'last_name' => ArrayHelper::getValue($attributes, 'given_name', ArrayHelper::getValue($attributes, 'name', ' ')),
            'status' => UserProfile::STATUS_ACTIVE,
            'profile_img' => ArrayHelper::getValue($attributes, 'picture')
        ];
        if ($this->client->getId() == 'vnconnect') {
            $name =  ArrayHelper::getValue($attributes, 'HoVaTen', ' ');
            $nameArr = explode(' ', $name);
            $attrs['first_name'] = array_pop($nameArr);
            $attrs['last_name'] = implode(" ", $nameArr);
        }
        $us->setAttributes($attrs);
        $us->save();
    }

    protected function login($id)
    {
        if (empty($id)) {
            return false;
        }
        $authzModule = Yii::$app->getModule('yii2-authz');
        $userIdentityClass = $authzModule->userIdentityClass;
        $identity = $userIdentityClass::findIdentity($id);
        if (empty($identity)) {
            return false;
        }
        return Yii::$app->user->login($identity, 3600 * 24);
    }
}

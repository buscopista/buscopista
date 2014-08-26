<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ForgotForm;
use app\models\AccountForm;
use app\models\PasswordForm;
use app\models\User;

class AccountController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'You need to login first'));
            return $this->redirect(['/account/login']);
        }
        
        $model = new AccountForm();
        
        // Default values
        $userCopy = clone Yii::$app->user->getIdentity();
        $model->setAttributes(array(
            'username' => $userCopy->username,
            'mail'     => $userCopy->mail,
        ));
        
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Your account has been successfully updated.'));
            // If user changes mail...
            if ($userCopy->mail !== $model->mail) {
                // Deactive user
                $userCopy->deactivate();
                $userCopy->save();
                // Send confirmation mail again
                $obj = new RegisterForm();
                $obj->sendConfirmationMail($userCopy);
                // Logout
                Yii::$app->user->logout();
                // TODO show a message...
                return $this->goHome();
            }            
        }
        
        return $this->render('index', [
            'model' => $model
        ]);
    }
    
    public function actionPassword()
    {
        if (\Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'You need to login first'));
            return $this->redirect(['/account/login']);
        }
        
        $model = new PasswordForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Your password has been successfully changed.'));
        } else {
            $model->reset(); // Security issues...
        }
        
        return $this->render('password', [
            'model' => $model,
        ]);
    }
    
    public function actionRegister()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new RegisterForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'You have been successfully signed up. We have sent you a confirmation e-mail to your account.'));
            return $this->goBack();
        } else {
            return $this->render('register', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionConfirm()
    {
        if (\Yii::$app->user->isGuest) {
            $username = Yii::$app->request->get('username');
            $user = $username ? User::findByUsername($username) : null;
            $user ? $user->setScenario(User::SCENARIO_CONFIRM) : null;
            $token = Yii::$app->request->get('token');
            
            if ($user && $token && $user->confirmToken === $token) { 
                // Activate user
                $user->activate();
                $user->save();
                // Notify user
                Yii::$app->session->setFlash('success', Yii::t('app', 'Account succesfully verified. Please enter your credentials to sign in.'));
                return $this->redirect(['/account/login']);
            } else {
                Yii::error("Username '{$username}' and confirmToken '{$token}' doesn't match");
                Yii::$app->session->setFlash('error', Yii::t('app', 'Wrong URL params. Please contact to support team for more information.'));
                return $this->goHome();
            }
        } else {
            Yii::$app->session->setFlash('info', Yii::t('app', 'Your account is already verified.'));
            return $this->goBack();
        }
    }
    
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    public function actionForgot()
    {
        $model = new ForgotForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->forgot()) {
            Yii::$app->session->setFlash('info', Yii::t('app', 'We have sent you an e-mail to your account'));
            return $this->goBack();
        } else {
            return $this->render('forgot', [
                'model' => $model,
            ]);
        }
    }
}

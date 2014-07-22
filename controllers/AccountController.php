<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ForgotForm;
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
        
        // TODO view user account
    }
    
    public function actionRegister()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new RegisterForm();
        $email = Yii::$app->params['supportEmail'];
        
        if ($model->load(Yii::$app->request->post()) && $model->register($email)) {
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
            $token = Yii::$app->request->get('token');
            
            if ($user && $token && $user->confirmToken === $token) {
                // Activate user account
                $user->activate();
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
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new ForgotForm();
        $email = Yii::$app->params['supportEmail'];
        
        if ($model->load(Yii::$app->request->post()) && $model->forgot($email)) {
            Yii::$app->session->setFlash('info', Yii::t('app', 'We have sent you an e-mail to your account'));
            return $this->goBack();
        } else {
            return $this->render('forgot', [
                'model' => $model,
            ]);
        }
    }
}

<?php
namespace app\components;

use app\helpers\Tools;
use app\models\File;
use Yii;
use yii\base\Component;
use yii\web\UploadedFile;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;

class FileService extends Component
{
    public $accessKey = 'svUrrWvsAiMgwOGcYE5VwHE9KfFKuy_aZ_NGuFuE';
    public $secretKey = 'QzoW6iNNbz2uUY0X9r4MiNqVBCk2FeUiNbEqIwUh';
    public $bucket = 'wqiang';
    public $allowExtension = ['jpg', 'png', 'jpeg', 'gif'];
    public $allowSize = 2;  //单位: M


    public function saveToDb(UploadedFile $uploadedFile, $options)
    {
        $file = new File();
        $attributes = [
            'name' => $options['newName'],
            'old_name' => $uploadedFile->name,
            'size' => $uploadedFile->size,
            'type' => $uploadedFile->type,
            'prefix' => $options['prefix'],
        ];
        $file->setAttributes($attributes, false);
        $file->save();
        return json_encode(['url' => $file->url, 'id' => $file->id]);
    }

    public function saveToQiNiu(UploadedFile $uploadedFile, $id = 0)
    {
        if ($uploadedFile) {
            if ($uploadedFile->size > $this->allowSize * 1024 * 1024) {
                return ['success' => false, 'msg' => '图片不能大于2M'];
            }
            if (!in_array($uploadedFile->extension, $this->allowExtension)) {
                return ['success' => false, 'msg' => '文件格式不正确!'];
            }
            $newName = 'editor/' . $id . '_' . Tools::getRandChar(4) . '.' . $uploadedFile->extension;
            $auth = new Auth($this->accessKey, $this->secretKey);
            $token = $auth->uploadToken($this->bucket);
            $upManager = new UploadManager();
            list($ret, $err) = $upManager->putFile($token, $newName, $uploadedFile->tempName, null, $uploadedFile->type);
            if ($err !== null) {
                return ['success' => false, 'msg' => '上传到七牛失败!'];
            } else {
                $file_path = Yii::$app->params['qiniu_dm'] . $newName;
                return ['success' => true, 'file_path' => $file_path];
            }
        }
    }

    public function search($where = [])
    {
        $query = File::find();
        if (isset($where['id']) && $where['id']) {
            $query->andFilterWhere(['id' => $where['id']]);
        }
        return $query;
    }


}

?>

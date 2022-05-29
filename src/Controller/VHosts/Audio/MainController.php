<?php
namespace App\Controller\Vhosts\Audio;

use Cake\Controller\Controller;
use Cake\Datasource\Exception\RecordNotFoundException;

class MainController extends Controller
{
    private $notFoundMessage = '<html>
<head><title>404 Not Found</title></head>
<body bgcolor="white">
<center><h1>404 Not Found</h1></center>
<hr><center>Tatoeba</center>
</body>
</html>
';

    public function initialize() {
        $this->autoRender = false;
    }

    public function legacy_audio_url($lang = null, $sentence_id = null)
    {
        $this->loadModel('Audios');
        try {
            $audio = $this->Audios
                ->find()
                ->select(['id', 'sentence_id'])
                ->where(compact('sentence_id'))
                ->innerJoinWith('Sentences', function ($q) use ($lang) {
                    return $q->where(['lang' => $lang]);
                })
                ->order(['Audios.id' => 'ASC'])
                ->firstOrFail();

            return $this->getResponse()
                        ->withFile($audio->file_path);
        } catch (RecordNotFoundException $e) {
            return $this->default();
        }
    }

    public function default() {
        return $this->getResponse()
            ->withStatus(404)
            ->withStringBody($this->notFoundMessage)
            ->withLength(strlen($this->notFoundMessage));
    }
}

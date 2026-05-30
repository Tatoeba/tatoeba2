<?php
namespace App\Controller\Vhosts\Audio;

use Cake\Controller\Controller;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;

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

    private $noReuseMessage = '<html>
<head><title>403 Forbidden</title></head>
<body bgcolor="white">
<center><h1>403 Forbidden</h1></center>
<center><p>The audio author does not allow reuse outside of Tatoeba.</p></center>
<hr><center>Tatoeba</center>
</body>
</html>
';

    public function initialize(): void {
        $this->autoRender = false;
    }

    public function legacy_audio_url($lang = null, $sentence_id = null)
    {
        $audios = $this->fetchTable('Audios')
            ->find('withLicense')
            ->select(['id', 'sentence_id'])
            ->where(compact('sentence_id'))
            ->innerJoinWith('Sentences', function ($q) use ($lang) {
                return $q->where(['lang' => $lang]);
            })
            ->order(['Audios.id' => 'ASC'])
            ->all();

        if (count($audios) == 0) {
            return $this->default();
        }

        foreach ($audios as $audio) {
            if ($audio->license) {
                return $this->getResponse()
                            ->withFile($audio->file_path);
            }
        }
        return $this->getResponse()
            ->withStatus(403)
            ->withStringBody($this->noReuseMessage)
            ->withLength(strlen($this->noReuseMessage));
    }

    public function default() {
        return $this->getResponse()
            ->withStatus(404)
            ->withStringBody($this->notFoundMessage)
            ->withLength(strlen($this->notFoundMessage));
    }
}

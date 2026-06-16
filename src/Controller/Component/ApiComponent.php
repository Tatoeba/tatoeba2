<?php
namespace App\Controller\Component;

use App\Search\Exception\SearchQueryException;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Http\CallbackStream;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Routing\Router;

class ApiComponent extends Component
{
    public static function decodeQueryParameters(string $query): array
    {
        $query  = strlen($query) == 0 ? [] : explode('&', $query);
        $params = [];
        foreach ($query as $param) {
            $parts = explode('=', $param, 2);
            if (count($parts) == 1) {
                $value = null;
                $name = $parts[0];
            } else {
                list($name, $value) = $parts;
            }
            $uname = urldecode($name);
            if (isset($params[$uname])) {
                if (is_array($params[$uname])) {
                    $params[$uname][] = urldecode($value);
                } else {
                    $params[$uname] = [$params[$uname], urldecode($value)];
                }
            } else {
                $params[$uname] = urldecode($value);
            }
        }
        return $params;
    }

    public static function encodeQueryParameters(array $params): string
    {
        $params = array_map(
            function($name, $values) {
                $name = urlencode($name);
                if (is_null($values)) {
                    return $name;
                } else {
                    $values = array_map(
                        function ($value) use ($name) {
                            return $name.'='.urlencode($value);
                        },
                        (array)$values
                    );
                    return implode('&', $values);
                }
            },
            array_keys($params),
            array_values($params)
        );
        return implode('&', $params);
    }

    /* We use our own query parsing functions here because PHP builtins
     * are not very flexible. In particular, PHP's parse_str() does not
     * handle well multiple parameters with the same name. See:
     *   https://www.php.net/manual/en/function.parse-str.php#76792
     */
    private function decodeQuery()
    {
        $request = $this->getController()->getRequest();
        $params = self::decodeQueryParameters($request->getUri()->getQuery());
        $this->getController()->setRequest($request->withQueryParams($params));
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        $this->decodeQuery();
    }

    /**
     * Build a full base URL based on the parameters of current request,
     * optionally modified by $newParams.
     */
    private function buildUrl($newParams = []): string
    {
        $params = $this->getController()->getRequest()->getQueryParams();
        foreach ($newParams as $newParam => $newValue) {
            if (is_null($newValue)) {
                unset($params[$newParam]);
            } else {
                $params[$newParam] = $newValue;
            }
        }
        $query = self::encodeQueryParameters($params);
        $url = Router::url(['?' => null], true);
        $url .= rtrim('?'.$query, '?');
        return $url;
    }

    private function getPaging(int $numResults, int $totalResults, ?string $cursorEnd)
    {
        $paging = [];

        $after = $this->getController()->getRequest()->getQuery('after');
        if (is_null($after)) {
            $paging['total'] = $totalResults;
        } else {
            $paging['first'] = self::buildUrl(['after' => null]);
        }

        $hasNext = $totalResults > $numResults;
        $paging['has_next'] = $hasNext;
        if ($hasNext && !is_null($cursorEnd)) {
            $paging['next'] = self::buildUrl(['after' => $cursorEnd]);
        }

        return $paging;
    }

    /**
     * Returns paginated results as JSON stream. This allows to return large
     * responses without having to keep the whole response into memory, but
     * streaming it bit by bit as results are coming out of $query.
     */
    public function paginatedResponse(Query $query, callable $cursorEndCb, callable $numResultsCb = null): Response
    {
        $stream = new CallbackStream(function () use ($query, $cursorEndCb, $numResultsCb) {
            $isFirst = true;
            $numResults = 0;
            $result = null;
            try {
                foreach ($query->enableBufferedResults(false) as $result) {
                    if ($isFirst) {
                        echo '{"data":[';
                        $isFirst = false;
                    } else {
                        echo ',';
                    }
                    echo json_encode($result);
                    $numResults++;
                }
            } catch (SearchQueryException $e) {
                throw new InternalErrorException($e->getMessage());
            }

            if ($isFirst) {
                echo '{"data":[';
            }
            echo ']';

            if ($numResultsCb) {
                $numResults = $numResultsCb();
            }
            $paging = $this->getPaging($numResults, $query->count(), $cursorEndCb($result));
            $paging = json_encode($paging);
            echo ",\"paging\":$paging}";
        });

        $this->autoRender = false;

        return $this
            ->getController()
            ->getResponse()
            ->withType('application/json')
            ->withBody($stream);
    }
}

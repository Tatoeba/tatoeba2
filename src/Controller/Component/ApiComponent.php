<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;

class ApiComponent extends Component
{
    public static function decodeQueryParameters(string $query): array
    {
        $query  = explode('&', $query);
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

    public function beforeFilter(Event $event)
    {
        $this->decodeQuery();
    }
}

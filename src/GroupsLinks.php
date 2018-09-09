<?
namespace nik54rus\links;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class GroupsLinks extends Widget  
{
    public $items;
    public $route;
    public $params;
    public $options;

    public function init(){
        parent::init();
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        Html::addCssClass($this->options, ['widget' => 'btn-group']);
    }

    public function run(){
    	return $this->renderItems();
    }

    public function renderItems()
    {
        $items = [];
        foreach ($this->items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $items[] = $this->renderItem($item);
        }

        return Html::tag('div', implode("\n", $items), $this->options);
    }

     public function renderItem($item)
    {
        if (is_string($item)) {
            return $item;
        }
        
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }

        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        
        $url = ArrayHelper::getValue($item, 'url', '#');

        $linkOptions = ArrayHelper::getValue($item, 'options', []);

        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        if ($active) {
            Html::addCssClass($linkOptions, 'active');
        }

        return Html::a($label, $url, $linkOptions);;
    }

    protected function isItemActive($item)
    {

        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
} 
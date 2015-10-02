<?php

use yupe\components\controllers\FrontController;

class ProductController extends FrontController
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var AttributeFilter
     */
    protected $attributeFilter;

    public function init()
    {
        $this->productRepository = Yii::app()->getComponent('productRepository');
        $this->attributeFilter = Yii::app()->getComponent('attributesFilter');

        parent::init();
    }

    public function actionIndex()
    {
        $data = Yii::app()->getRequest()->getQueryString() ? $this->productRepository->getByFilter(
            $this->attributeFilter->getMainAttributesForSearchFromQuery(Yii::app()->getRequest()),
            $this->attributeFilter->getEavAttributesForSearchFromQuery(Yii::app()->getRequest())
        ) : $this->productRepository->getListForIndexPage();

        $this->render('index', ['dataProvider' => $data]);
    }

    /**
     * @param $name
     * @throws CHttpException
     */
    public function actionView($name)
    {
        $product = $this->productRepository->getBySlug($name);

        if (null === $product) {
            throw new CHttpException(404, Yii::t('StoreModule.catalog', 'Product was not found!'));
        }

        $this->render('view', ['product' => $product]);
    }

    public function actionSearch()
    {
        if (!Yii::app()->getRequest()->getQuery('SearchForm')) {
            throw new CHttpException(404);
        }

        $form = new SearchForm();

        $form->setAttributes(Yii::app()->getRequest()->getQuery('SearchForm'));

        if ($form->validate()) {

            $category = $form->category ? StoreCategory::model()->findByPk($form->category) : null;

            $this->render(
                'search',
                [
                    'category' => $category,
                    'searchForm' => $form,
                    'dataProvider' => $this->productRepository->getByFilter(
                        $this->attributeFilter->getMainAttributesForSearchFromQuery(Yii::app()->getRequest(), [AttributeFilter::MAIN_SEARCH_PARAM_NAME => $form->q]),
                        $this->attributeFilter->getEavAttributesForSearchFromQuery(Yii::app()->getRequest())
                    )
                ]
            );
        }
    }

    /**
     *
     */
    public function actionAutocomplete()
    {
        $query = Yii::app()->getRequest()->getQuery('term');

        $result = [];

        if (strlen($query) > 2) {
            $result = $this->productRepository->search($query);
        }

        Yii::app()->ajax->raw($result);
    }
}
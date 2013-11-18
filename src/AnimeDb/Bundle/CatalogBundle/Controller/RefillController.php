<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Episodes as EpisodesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Gengres as GengresForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Names as NamesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Summary as SummaryForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * Refill
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class RefillController extends Controller
{
    /**
     * Refill item
     *
     * @param string $plugin
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refillAction($plugin, $field, Item $item, Request $request)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->fillItemFromRequest($item, $request);

        $form = $this->getForm($field, $refiller->refill($item, $field));

        return $this->render('AnimeDbCatalogBundle:Refill:refill.html.twig', [
            'field' => $field,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Search for refill
     *
     * @param string $plugin
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction($plugin, $field, Item $item, Request $request)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->fillItemFromRequest($item, $request);

        $result = array();
        if ($refiller->isCanSearch($item, $field)) {
            $result = $refiller->search($item, $field);
            /* @var $search_item \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Item */
            foreach ($result as $key => $search_item) {
                $result[$key] = [
                    'name' => $search_item->getName(),
                    'link' => $this->generateUrl('refiller_search_fill', [
                        'plugin' => $plugin,
                        'field' => $field,
                        'id' => $item->getId(),
                        'data' => $search_item->getData(),
                        'source' => $search_item->getSource(),
                    ])
                ];
            }
        }

        return $this->render('AnimeDbCatalogBundle:Refill:search.html.twig', [
            'result' => $result
        ]);
    }

    /**
     * Refill item from search result
     *
     * @param string $plugin
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fillFromSearchAction($plugin, $field, Item $item, Request $request)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->fillItemFromRequest($item, $request);

        $form = $this->getForm($field, $refiller->refillFromSearchResult($item, $field, $request->get('data')));

        return $this->render('AnimeDbCatalogBundle:Refill:refill.html.twig', [
            'field' => $field,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Get form for field
     *
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($field, Item $item)
    {
        switch ($field) {
            case Refiller::FIELD_EPISODES:
                $form = new EpisodesForm();
                $data = ['episodes' => $item->getEpisodes()];
                break;
            case Refiller::FIELD_GENRES:
                $form = new GengresForm();
                $data = ['genres' => $item->getGenres()];
                break;
            case Refiller::FIELD_NAMES:
                $form = new NamesForm();
                $data = ['names' => $item->getNames()];
                break;
            case Refiller::FIELD_SUMMARY:
                $form = new SummaryForm();
                $data = ['summary' => $item->getSummary()];
                break;
            default:
                throw $this->createNotFoundException('Field \''.$field.'\' is not supported');
        }
        // search new source link
        foreach ($item->getSources() as $source) {
            if ($source->getId()) {
                $data['source'] = $source->getUrl();
                break;
            }
        }

        return $this->createForm($form, $data);
    }

    /**
     * Fill the item from the request
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected function fillItemFromRequest(Item $item, Request $request)
    {
        $request = clone $request;
        $request->request->replace($request->query->all());
        $request->setMethod('POST');
        $this->createForm('anime_db_catalog_entity_item', $item)
            ->handleRequest($request);
        return $item;
    }
}
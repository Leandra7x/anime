<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AnimeDB\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\HttpFoundation\Request;
use AnimeDB\Bundle\CatalogBundle\Form\Entity\Storage as StorageForm;

/**
 * Storages
 *
 * @package AnimeDB\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StorageController extends Controller
{
    /**
     * Storages list
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository('AnimeDBCatalogBundle:Storage');
        $storages = $repository->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('AnimeDBCatalogBundle:Storage:list.html.twig', [
            'storages' => $storages
        ]);
    }

    /**
     * Change storages
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Storage|null $storage
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeAction(Storage $storage = null, Request $request)
    {
        if (!$storage) {
            throw $this->createNotFoundException('Storage no found');
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new StorageForm(), $storage);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($storage);
                $em->flush();
                return $this->redirect($this->generateUrl('storage_list'));
            }
        }

        return $this->render('AnimeDBCatalogBundle:Storage:change.html.twig', [
            'storage' => $storage,
            'form' => $form->createView()
        ]);
    }

    /**
     * Add storages
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $storage = new Storage();

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new StorageForm(), $storage);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($storage);
                $em->flush();
                return $this->redirect($this->generateUrl('storage_list'));
            }
        }

        return $this->render('AnimeDBCatalogBundle:Storage:add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete storages
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Storage|null $storage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Storage $storage = null)
    {
        if (!$storage) {
            throw $this->createNotFoundException('Storage no found');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($storage);
        $em->flush();
        return $this->redirect($this->generateUrl('storage_list'));
    }
}
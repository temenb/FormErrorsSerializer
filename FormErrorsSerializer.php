<?php
/**
 * Created by PhpStorm.
 * User: Temchenko Aleksey <temenb@gmail.com>
 * Date: 2/23/15
 * Time: 8:15 PM
 *
 * Forked from https://gist.github.com/Graceas/6505663
 *
 * In action you may use:
 *
 * $ajax = $request->isXmlHttpRequest();
 * if ($request->getMethod() == 'POST') {
 *     $form->handleRequest($request);
 *
 *     if ($form->isValid()) {
 *         // ...
 *     } else {
 *         if ($ajax) {
 *             $errors = $this->get('form_serializer')->serializeFormErrors($form, true, true);
 *
 *             return new Response(json_encode(array(
 *                 'status' => 'error',
 *                 'errors' => $errors
 *             )));
 *         }
 *     }
 * }
 *
 */

namespace Temenb;

use Symfony\Component\Form\Form;

/**
 *
 * Class FormErrorsSerializer
 * @package FttBundle\Service
 */
class FormErrorsSerializer
{

    /**
     * @param Form $form
     * @param bool $flatArray
     * @param bool $addFormName
     * @param string $glueKeys
     * @return array
     */
    public function serializeFormErrors(Form $form, $flatArray = false, $addFormName = false, $glueKeys = '_')
    {
        $errors = array();
        $errors['global'] = array();
        $errors['fields'] = array();

        foreach ($form->getErrors() as $error) {
            $errors['global'][] = $error->getMessage();
        }

        $errors['fields'] = $this->serialize($form);

        if ($flatArray) {
            $errors['fields'] = $this->arrayFlatten(
                $errors['fields'],
                $glueKeys,
                (($addFormName) ? $form->getName() : '')
            );
        }


        return $errors;
    }

    /**
     * @param Form $form
     * @return array
     */
    private function serialize(Form $form)
    {
        $localErrors = array();
        foreach ($form->getIterator() as $key => $child) {
            foreach ($child->getErrors() as $error) {
                $localErrors[$key] = $error->getMessage();
            }

            if (($child instanceof Form) && (count($child->getIterator()) > 0)) {
                $localErrors[$key] = $this->serialize($child);
            }
        }

        return $localErrors;
    }

    /**
     * @param $array
     * @param string $separator
     * @param string $flattenedKey
     * @return array
     */
    private function arrayFlatten($array, $separator = "_", $flattenedKey = '')
    {
        $flattenedArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattenedArray = array_merge(
                    $flattenedArray,
                    $this->arrayFlatten(
                        $value,
                        $separator,
                        (strlen($flattenedKey) > 0 ? $flattenedKey . $separator : "") . $key
                    )
                );

            } else {
                $flattenedArray[(strlen($flattenedKey) > 0 ? $flattenedKey . $separator : "") . $key] = $value;
            }
        }
        return $flattenedArray;
    }
}

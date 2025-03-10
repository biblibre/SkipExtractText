<?php

namespace SkipExtractText;

use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\Event;
use Doctrine\Common\Collections\Criteria;
use Omeka\Module\AbstractModule;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach('*', 'api.get_fulltext_text.value_criteria', [$this, 'onApiGetFulltextTextValueCriteria']);
    }

    public function onApiGetFulltextTextValueCriteria(Event $event)
    {
        $criteria = $event->getParam('criteria');
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $vocabulary = $em->getRepository('Omeka\Entity\Vocabulary')->findOneBy(['prefix' => 'extracttext']);

        if ($vocabulary) {
            $property = $em->getRepository('Omeka\Entity\Property')->findOneBy(['vocabulary' => $vocabulary->getId(), 'localName' => 'extracted_text']);
            if ($property) {
                $criteria->andWhere(Criteria::expr()->neq('property', $property));
            }
        }
    }
}

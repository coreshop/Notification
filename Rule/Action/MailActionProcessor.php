<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Notification\Rule\Action;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use Pimcore\Mail;
use Pimcore\Model\Document;

class MailActionProcessor implements NotificationRuleProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply($subject, NotificationRuleInterface $rule, array $configuration, $params = [])
    {
        $language = null;
        $mails = $configuration['mails'];

        if (array_key_exists('_locale', $params)) {
            $language = $params['_locale'];
        }

        if (is_null($language)) {
            throw new \Exception('MailActionProcessor: Language is not set.');
        }

        if (array_key_exists($language, $mails)) {
            $mailDocumentId = $mails[$language];
            $mailDocument = Document::getById($mailDocumentId);
            $recipient = $params['recipient'];

            $params['rule'] = $rule;

            unset($params['recipient'], $params['_locale']);

            if ($mailDocument instanceof Document\Email) {
                $mail = new Mail();
                $params['object'] = $subject;

                if ($recipient) {
                    $mail->setTo($recipient);
                }

                $mail->setDocument($mailDocument);
                $mail->setParams($params);

                $mail->send();
            }
        }
    }
}

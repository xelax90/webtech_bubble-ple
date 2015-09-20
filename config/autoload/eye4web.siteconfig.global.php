<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

use SkelletonApplication\Entity\SiteConfig;
use SkelletonApplication\SiteConfig\Reader\DoctrineORMReader;

return [
    'eye4web' => [
        'site-config' => [
            /**
             *
             */
            'doctrineORMEntityClass' => SiteConfig::class,

            /**
             * You can use any class implementing either of the two interfaces:
             * \Eye4web\SiteConfig\Reader\ReaderInterface
             * \Zend\Config\Read\ReaderInterface
             */
            'readerClass' => DoctrineORMReader::class,

            /**
             * If you use a reader implementing Zend\Config\Reader\ReaderInterface class,
             * you must specify the file path here
             */
            'configFile' => null,
        ]
    ],
	
	// ignore default SiteConfig entity
    'doctrine' => [
        'driver' => [
            'eye4web_siteconfig_driver' => null,
            'orm_default' => [
                'drivers' => [
                    'Eye4web\SiteConfig' => null,
                ],
            ],
        ],
    ],
];

<?php

namespace MediaWiki\Extension\IslamDashboard\Tests\Unit\Helpers;

use MediaWiki\MediaWikiServices;
use User;
use RequestContext;
use OutputPage;
use Title;
use MediaWiki\User\UserIdentityValue;

/**
 * Trait for setting up common test services
 */
trait DummyServicesTrait {

    /**
     * Create a test user with specified options
     *
     * @param array $options Options for the user
     * @return User
     */
    protected function createTestUser( array $options = [] ) {
        $user = $this->getMockBuilder( User::class )
            ->disableOriginalConstructor()
            ->getMock();
        
        // Set up default user options
        $user->method( 'isAllowed' )
            ->willReturn( $options['allowed'] ?? true );
            
        $user->method( 'isAnon' )
            ->willReturn( $options['anon'] ?? false );
            
        $user->method( 'getName' )
            ->willReturn( $options['name'] ?? 'TestUser' );
            
        $user->method( 'getId' )
            ->willReturn( $options['id'] ?? 1 );
            
        $user->method( 'isRegistered' )
            ->willReturn( !($options['anon'] ?? false) );
            
        return $user;
    }

    /**
     * Create a mock OutputPage object
     *
     * @param array $options Options for the output page
     * @return OutputPage
     */
    protected function getTestOutputPage( array $options = [] ) {
        $outputPage = $this->createMock( OutputPage::class );
        
        // Set up default output page options
        $outputPage->method( 'getTitle' )->willReturn( 
            $options['title'] ?? $this->createMock( Title::class )
        );
        
        return $outputPage;
    }

    /**
     * Set up a test context with a user and output page
     *
     * @param array $options Options for the context
     * @return RequestContext
     */
    protected function getTestContext( array $options = [] ) {
        $context = new RequestContext();
        
        if ( isset( $options['user'] ) ) {
            $context->setUser( $options['user'] );
        } else {
            $context->setUser( $this->createTestUser( $options['userOptions'] ?? [] ) );
        }
        
        if ( isset( $options['output'] ) ) {
            $context->setOutput( $options['output'] );
        } else {
            $context->setOutput( $this->getTestOutputPage( $options['outputOptions'] ?? [] ) );
        }
        
        return $context;
    }

    /**
     * Get a mock MediaWikiServices instance
     *
     * @return MediaWikiServices
     */
    protected function getTestServices() {
        return MediaWikiServices::getInstance();
    }
}

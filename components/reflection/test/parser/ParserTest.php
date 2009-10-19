<?php
/**
 * I provide completely working code within this article, which will not be
 * developed any further, because there are already existing packages, which try
 * to provide similar functionallities.
 */

namespace de\buzz2ee\reflection\parser;

use \de\buzz2ee\reflection\StaticReflectionClass;
use \de\buzz2ee\reflection\StaticReflectionInterface;
use \de\buzz2ee\reflection\StaticReflectionMethod;
use \de\buzz2ee\reflection\StaticReflectionProperty;

require_once 'BaseTest.php';

/**
 * Test cases for the parser class.
 *
 * @author  Manuel Pichler <mapi@pdepend.org>
 * @license Copyright by Manuel Pichler
 * @version $Revision$
 */
class ParserTest extends \de\buzz2ee\reflection\BaseTest
{
    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserReturnsInstanceOfTypeStaticClass()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithoutNamespace' );
        $this->assertType( StaticReflectionClass::TYPE, $parser->parse() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassParentByDefaultWithNull()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithoutNamespace' );
        $this->assertNull( $parser->parse()->getParentClass() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassWithoutInterfaceByDefaultAsEmpty()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithoutNamespace' );
        $this->assertSame( array(), $parser->parse()->getInterfaces() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassWithParentClass()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithParentClass' );
        $this->assertType( StaticReflectionInterface::TYPE, $parser->parse()->getParentClass() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesCommentInParentClassName()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithCommentInParentClassName' );
        $this->assertSame( 'c\w\n\ClassWithNamespace', $parser->parse()->getParentClass()->getName() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassWithImplementedInterface()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithImplementedInterface' );

        $interfaces = $parser->parse()->getInterfaces();
        $this->assertType( StaticReflectionInterface::TYPE, $interfaces[0] );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassWithMultipleImplementedInterfaces()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithMultipleImplementedInterfaces' );
        $this->assertSame( 2, count( $parser->parse()->getInterfaces() ) );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserReturnsInstanceOfTypeStaticInterface()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InterfaceWithoutNamespace' );
        $this->assertType( StaticReflectionInterface::TYPE, $parser->parse() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesInterfaceWithParentInterface()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InterfaceWithParentInterface' );

        $interfaces = $parser->parse()->getInterfaces();
        $this->assertType( StaticReflectionInterface::TYPE, $interfaces[0] );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesInterfaceWithMultipleParentInterfaces()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InterfaceWithMultipleParentInterfaces' );

        $interfaces = $parser->parse()->getInterfaces();
        $this->assertSame( 2, count( $interfaces ) );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassUseStatementAsExpected()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassUseStatement' );

        $interfaces = $parser->parse()->getInterfaces();
        $this->assertSame( 'foo\InterfaceWithNamespace', $interfaces[0]->getName() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassUseStatementWithAliasAsExpected()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassAliasedUseStatement' );

        $interfaces = $parser->parse()->getInterfaces();
        $this->assertSame( 'foo\InterfaceWithNamespace', $interfaces[0]->getName() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassWithMultipleUseStatements()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithMultipleUseStatement' );

        $class = $parser->parse();
        $this->assertSame( 'c\w\n\ClassWithNamespace', $class->getParentClass()->getName() );

        $interfaces = $class->getInterfaces();
        $this->assertSame( 'foo\InterfaceWithNamespace', $interfaces[0]->getName() );
        $this->assertSame( 'InterfaceWithoutNamespace', $interfaces[1]->getName() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesRegularMethodInClassAsExpected()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithMethod' );

        $class = $parser->parse();
        $this->assertSame( 1, count( $class->getMethods() ) );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesClassWithMultipleMethods()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithMultipleMethods' );

        $class = $parser->parse();
        $this->assertSame( 3, count( $class->getMethods() ) );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesInterfaceWithMultipleMethodDeclarations()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InterfaceWithMultipleMethods' );
        $this->assertSame( 3, count( $parser->parse()->getMethods() ) );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserSetsClassDocComment()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithDocComment' );
        $this->assertSame(
            "/**\n" .
            " * Hello Static Reflection\n" .
            " *\n" .
            " * @author Manuel Pichler\n" .
            " */",
            $parser->parse()->getDocComment()
        );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsClassAsAbstract()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassDeclaredAbstract' );
        $this->assertTrue( $parser->parse()->isAbstract() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsClassAsFinal()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassDeclaredFinal' );
        $this->assertTrue( $parser->parse()->isFinal() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserSetsMethodDocComment()
    {
        $parser = new Parser( $this->createSourceResolver(), 'MethodWithComment' );
        $this->assertSame(
            "/**\n" .
            "     * A simple method...\n" .
            "     *\n" .
            "     * @return void\n" .
            "     */",
            $parser->parse()->getMethod( 'foo' )->getDocComment()
        );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsMethodAsPublic()
    {
        $parser = new Parser( $this->createSourceResolver(), 'MethodPublic' );
        $this->assertTrue( $parser->parse()->getMethod( 'foo' )->isPublic() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsMethodAsProtected()
    {
        $parser = new Parser( $this->createSourceResolver(), 'MethodProtected' );
        $this->assertTrue( $parser->parse()->getMethod( 'foo' )->isProtected() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsMethodAsPrivate()
    {
        $parser = new Parser( $this->createSourceResolver(), 'MethodPrivate' );
        $this->assertTrue( $parser->parse()->getMethod( 'foo' )->isPrivate() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsMethodAsFinal()
    {
        $parser = new Parser( $this->createSourceResolver(), 'MethodFinal' );
        $this->assertTrue( $parser->parse()->getMethod( 'foo' )->isFinal() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsMethodAsStatic()
    {
        $parser = new Parser( $this->createSourceResolver(), 'MethodStatic' );
        $this->assertTrue( $parser->parse()->getMethod( 'foo' )->isStatic() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsMethodAsAbstract()
    {
        $parser = new Parser( $this->createSourceResolver(), 'MethodAbstract' );
        $this->assertTrue( $parser->parse()->getMethod( 'foo' )->isAbstract() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserSetsPropertyDocComment()
    {
        $parser = new Parser( $this->createSourceResolver(), 'PropertyWithComment' );
        $this->assertSame(
            "/**\n" .
            "     * The answer...\n" .
            "     *\n" .
            "     * @var integer\n" .
            "     */",
            $parser->parse()->getProperty( 'foo' )->getDocComment()
        );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesCommaSeparatedProperties()
    {
        $parser     = new Parser( $this->createSourceResolver(), 'PropertyWithCommaSeparatedProperties' );
        $properties = $parser->parse()->getProperties();

        $this->assertSame( 3, count( $properties ) );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserHandlesPropertyWithConstantDefaultValue()
    {
        $parser = new Parser( $this->createSourceResolver(), 'PropertyWithConstantDefaultValue' );
        $this->assertType( StaticReflectionProperty::TYPE, $parser->parse()->getProperty( 'foo' ) );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsPropertyAsPrivate()
    {
        $parser = new Parser( $this->createSourceResolver(), 'PropertyPrivate' );
        $this->assertTrue( $parser->parse()->getProperty( 'foo' )->isPrivate() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsPropertyAsProtected()
    {
        $parser = new Parser( $this->createSourceResolver(), 'PropertyProtected' );
        $this->assertTrue( $parser->parse()->getProperty( 'foo' )->isProtected() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsPropertyAsPublic()
    {
        $parser = new Parser( $this->createSourceResolver(), 'PropertyPublic' );
        $this->assertTrue( $parser->parse()->getProperty( 'foo' )->isPublic() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserFlagsPropertyAsStatic()
    {
        $parser = new Parser( $this->createSourceResolver(), 'PropertyStatic' );
        $this->assertTrue( $parser->parse()->getProperty( 'foo' )->isStatic() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testParserSetsClassSourceFileName()
    {
        $parser = new Parser( $this->createSourceResolver(), 'ClassWithoutNamespace' );
        $this->assertSame( __FILE__, $parser->parse()->getFileName() );
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \RuntimeException
     */
    public function testParserThrowsExceptionForInvalidClassDeclaration()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InvalidClassDeclaration' );
        $parser->parse();
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \RuntimeException
     */
    public function testParserThrowsExceptionForInvalidInterfaceDeclaration()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InvalidInterfaceDeclaration' );
        $parser->parse();
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \RuntimeException
     */
    public function testParserThrowsExceptionForInvalidImplementedInterfaceDeclaration()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InvalidImplementedInterfaceDeclaration' );
        $parser->parse();
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \RuntimeException
     */
    public function testParserThrowsExceptionForUnclosedClassScope()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InvalidUnclosedClassScope' );
        $parser->parse();
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \RuntimeException
     */
    public function testParserThrowsExceptionForInvalidNamespaceDeclaration()
    {
        $parser = new Parser( $this->createSourceResolver(), 'foo\bar\InvalidNamespaceDeclaration' );
        $parser->parse();
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \RuntimeException
     */
    public function testParserThrowsExceptionForInvalidUseStatement()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InvalidUseStatement' );
        $parser->parse();
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \RuntimeException
     */
    public function testParserThrowsExceptionForInvalidMethodDeclatation()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InvalidMethodDeclaration' );
        $parser->parse();
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \RuntimeException
     */
    public function testParserThrowsExceptionForUnclosedMethodScope()
    {
        $parser = new Parser( $this->createSourceResolver(), 'InvalidUnclosedMethodScope' );
        $parser->parse();
    }

    /**
     * @return void
     * @covers \de\buzz2ee\reflection\parser\Parser
     * @group reflection
     * @group reflection::parser
     * @group unittest
     * @expectedException \LogicException
     */
    public function testParserThrowsExceptionWhenRequestClassDoesNotExist()
    {
        $parser = new Parser( $this->createSourceResolver(), 'NoClassDefined' );
        $parser->parse();
    }

    protected function createSourceResolver()
    {
        $resolver = $this->getMock( 'de\buzz2ee\reflection\interfaces\SourceResolver' );
        $resolver->expects( $this->any() )
            ->method( 'getPathnameForClass' )
            ->will( $this->returnValue( __FILE__ ) );
        $resolver->expects( $this->atLeastOnce() )
            ->method( 'getSourceForClass' )
            ->will( $this->returnCallback( array( $this, 'resolveSourceForClass' ) ) );
        return $resolver;
    }

    public function resolveSourceForClass( $className )
    {
        $localName = explode( '\\', $className );
        $localName = array_pop( $localName );

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( __DIR__ . '/../_source' )
        );

        foreach ( $files as $file )
        {
            if ( pathinfo( $file->getFilename(), PATHINFO_FILENAME ) == $localName )
            {
                return file_get_contents( $file->getRealpath() );
            }
        }
        throw new \ErrorException( 'Cannot locate source for class: ' . $className );
    }
}
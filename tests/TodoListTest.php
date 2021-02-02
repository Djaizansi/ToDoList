<?php

namespace App\Tests;

use App\Entity\Item;
use App\Entity\TodoList;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use DateTime;
use DateInterval;

class TodoListTest extends TestCase
{
    private $user;
    private $item;
    private $todolist;

    protected function setUp(): void
    {
        parent::setUp();

        $today = new DateTime('now',new \DateTimeZone('Europe/Paris'));
        $myToday = new DateTime('now',new \DateTimeZone('Europe/Paris'));
        $dateMock = new DateTime('now',new \DateTimeZone('Europe/Paris'));
        $birthday = $today->sub(new DateInterval('P30Y'))->format('Y-m-d');
        $createdItemAt = $myToday->add(new DateInterval('PT45M'));

        $this->user = new User(
            'Jallali',
            'Youcef',
            'youcef.jallali@gmail.com',
            'azfezfez3',
            $birthday
        );

        $this->item = new Item(
            'Exercice1 à faire',
            'petit exercice à faire pour les vacances',
            $createdItemAt
        );

        $unItem = new Item(
            'Mock',
            'petit exercice à faire pour les vacances',
            $dateMock
        );

        $this->todolist = $this->getMockBuilder(TodoList::class)
            ->onlyMethods(['getSizeTodoList','getLastItem'])
            ->getMock();

        $this->todolist->expects($this->any())->method('getLastItem')->willReturn($unItem);
        $this->todolist->setUser($this->user);
    }

    public function testCanAddItemNominal()
    {
        $this->todolist->expects($this->any())->method('getSizeTodoList')->willReturn('1');
        $canAddItem = $this->todolist->canAddItem($this->item);

        $this->assertNotNull($canAddItem);
        $this->assertEquals('Exercice1 à faire', $canAddItem->getName());
    }

    public function testCanAddMaxItem()
    {
        $this->todolist->expects($this->any())->method('getSizeTodoList')->willReturn('10');
        $this->expectException('Exception');
        $this->expectExceptionMessage('La todo list possède beaucoup d\'item');

        $canAddItem = $this->todolist->canAddItem($this->item);

        $this->assertTrue($canAddItem);
    }
}
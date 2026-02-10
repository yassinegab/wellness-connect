<?php

namespace App\Tests\Controller;

use App\Entity\UserWellBeingData;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserWellBeingDataControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $userWellBeingDatumRepository;
    private string $path = '/user/well/being/data/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->userWellBeingDatumRepository = $this->manager->getRepository(UserWellBeingData::class);

        foreach ($this->userWellBeingDatumRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UserWellBeingDatum index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user_well_being_datum[workEnvironment]' => 'Testing',
            'user_well_being_datum[sleepProblems]' => 'Testing',
            'user_well_being_datum[headaches]' => 'Testing',
            'user_well_being_datum[restlessness]' => 'Testing',
            'user_well_being_datum[heartbeatPalpitations]' => 'Testing',
            'user_well_being_datum[lowAcademicConfidence]' => 'Testing',
            'user_well_being_datum[classAttendance]' => 'Testing',
            'user_well_being_datum[anxietyTension]' => 'Testing',
            'user_well_being_datum[irritability]' => 'Testing',
            'user_well_being_datum[subjectConfidence]' => 'Testing',
            'user_well_being_datum[createdAt]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->userWellBeingDatumRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new UserWellBeingData();
        $fixture->setWorkEnvironment('My Title');
        $fixture->setSleepProblems('My Title');
        $fixture->setHeadaches('My Title');
        $fixture->setRestlessness('My Title');
        $fixture->setHeartbeatPalpitations('My Title');
        $fixture->setLowAcademicConfidence('My Title');
        $fixture->setClassAttendance('My Title');
        $fixture->setAnxietyTension('My Title');
        $fixture->setIrritability('My Title');
        $fixture->setSubjectConfidence('My Title');
        $fixture->setCreatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UserWellBeingDatum');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new UserWellBeingData();
        $fixture->setWorkEnvironment('Value');
        $fixture->setSleepProblems('Value');
        $fixture->setHeadaches('Value');
        $fixture->setRestlessness('Value');
        $fixture->setHeartbeatPalpitations('Value');
        $fixture->setLowAcademicConfidence('Value');
        $fixture->setClassAttendance('Value');
        $fixture->setAnxietyTension('Value');
        $fixture->setIrritability('Value');
        $fixture->setSubjectConfidence('Value');
        $fixture->setCreatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user_well_being_datum[workEnvironment]' => 'Something New',
            'user_well_being_datum[sleepProblems]' => 'Something New',
            'user_well_being_datum[headaches]' => 'Something New',
            'user_well_being_datum[restlessness]' => 'Something New',
            'user_well_being_datum[heartbeatPalpitations]' => 'Something New',
            'user_well_being_datum[lowAcademicConfidence]' => 'Something New',
            'user_well_being_datum[classAttendance]' => 'Something New',
            'user_well_being_datum[anxietyTension]' => 'Something New',
            'user_well_being_datum[irritability]' => 'Something New',
            'user_well_being_datum[subjectConfidence]' => 'Something New',
            'user_well_being_datum[createdAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/well/being/data/');

        $fixture = $this->userWellBeingDatumRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getWorkEnvironment());
        self::assertSame('Something New', $fixture[0]->getSleepProblems());
        self::assertSame('Something New', $fixture[0]->getHeadaches());
        self::assertSame('Something New', $fixture[0]->getRestlessness());
        self::assertSame('Something New', $fixture[0]->getHeartbeatPalpitations());
        self::assertSame('Something New', $fixture[0]->getLowAcademicConfidence());
        self::assertSame('Something New', $fixture[0]->getClassAttendance());
        self::assertSame('Something New', $fixture[0]->getAnxietyTension());
        self::assertSame('Something New', $fixture[0]->getIrritability());
        self::assertSame('Something New', $fixture[0]->getSubjectConfidence());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new UserWellBeingData();
        $fixture->setWorkEnvironment('Value');
        $fixture->setSleepProblems('Value');
        $fixture->setHeadaches('Value');
        $fixture->setRestlessness('Value');
        $fixture->setHeartbeatPalpitations('Value');
        $fixture->setLowAcademicConfidence('Value');
        $fixture->setClassAttendance('Value');
        $fixture->setAnxietyTension('Value');
        $fixture->setIrritability('Value');
        $fixture->setSubjectConfidence('Value');
        $fixture->setCreatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/user/well/being/data/');
        self::assertSame(0, $this->userWellBeingDatumRepository->count([]));
    }
}

<?php

namespace App\Tests\Controller;

use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RendezVousControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $rendezVouRepository;
    private string $path = '/rendez/vous/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->rendezVouRepository = $this->manager->getRepository(RendezVous::class);

        foreach ($this->rendezVouRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('RendezVou index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'rendez_vou[patientId]' => 'Testing',
            'rendez_vou[medcinId]' => 'Testing',
            'rendez_vou[hopitalId]' => 'Testing',
            'rendez_vou[typeConsultation]' => 'Testing',
            'rendez_vou[statut]' => 'Testing',
            'rendez_vou[scoreAI]' => 'Testing',
            'rendez_vou[createdAt]' => 'Testing',
            'rendez_vou[updatedAt]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->rendezVouRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new RendezVous();
        $fixture->setPatientId('My Title');
        $fixture->setMedcinId('My Title');
        $fixture->setHopitalId('My Title');
        $fixture->setTypeConsultation('My Title');
        $fixture->setStatut('My Title');
        $fixture->setScoreAI('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('RendezVou');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new RendezVous();
        $fixture->setPatientId('Value');
        $fixture->setMedcinId('Value');
        $fixture->setHopitalId('Value');
        $fixture->setTypeConsultation('Value');
        $fixture->setStatut('Value');
        $fixture->setScoreAI('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'rendez_vou[patientId]' => 'Something New',
            'rendez_vou[medcinId]' => 'Something New',
            'rendez_vou[hopitalId]' => 'Something New',
            'rendez_vou[typeConsultation]' => 'Something New',
            'rendez_vou[statut]' => 'Something New',
            'rendez_vou[scoreAI]' => 'Something New',
            'rendez_vou[createdAt]' => 'Something New',
            'rendez_vou[updatedAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/rendez/vous/');

        $fixture = $this->rendezVouRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getPatientId());
        self::assertSame('Something New', $fixture[0]->getMedcinId());
        self::assertSame('Something New', $fixture[0]->getHopitalId());
        self::assertSame('Something New', $fixture[0]->getTypeConsultation());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getScoreAI());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new RendezVous();
        $fixture->setPatientId('Value');
        $fixture->setMedcinId('Value');
        $fixture->setHopitalId('Value');
        $fixture->setTypeConsultation('Value');
        $fixture->setStatut('Value');
        $fixture->setScoreAI('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/rendez/vous/');
        self::assertSame(0, $this->rendezVouRepository->count([]));
    }
}

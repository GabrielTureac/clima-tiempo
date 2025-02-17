<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClimaController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $_ENV['OPENWEATHER_API_KEY'];
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): RedirectResponse
    {
        return $this->redirectToRoute('app_clima_index');
    }


    /**
     * @Route("/clima", name="app_clima_index")
     */
    public function index(Request $request): Response
    {
        $city = $request->query->get('city', 'Madrid');
        $weatherData = $this->getWeatherData($city);

        return $this->render('clima/index.html.twig', [
            'weather' => $weatherData,
            'city' => $city
        ]);
    }

    private function getWeatherData(string $city)
{
    try {
        // API de OpenWeather
        $response = $this->httpClient->request('GET', "https://api.openweathermap.org/data/2.5/weather", [
            'query' => [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'es',
            ]
        ]);

        return $response->toArray();
    } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
        return null;
    } catch (\Exception $e) {
        throw new \RuntimeException('Error al obtener los datos del clima. Intenta de nuevo más tarde.');
    }
}
}
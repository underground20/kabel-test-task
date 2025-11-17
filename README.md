Сервис представляет собой API, которое работает с
данными из вселенной Rick and Morty: персонажи, эпизоды и отзывы.
Данные импортируются из публичного API https://rickandmortyapi.com/documentation/#rest

<h3>API:</h3>
1. <code>POST /api/v1/episodes/{episodeId}/review</code> - добавление отзыва к эпизоду

Входные параметры (пример):
<pre>
{
    "author": "Ivan Ivanov",
    "text": "Simple review"
}
</pre>
Можно задать способ расчета рейтинга при добавление отзыва через env-переменную RATING_CALCULATE_METHOD:
- sentiment - на основе вх. текста отзыва генерирует рейтинг используя php-sentiment-analyzer
- random - генерирует рандомные значения


2. <code>GET /api/v1/episodes?season=2&date_from=2015-02-01&date_to=2025-02-02</code>
Пример ответа:
<pre>
{
  "info": {
    "total": 10,
    "page": 1,
    "pages": 1
  },
  items": [
    {
      "title": "A Rickle in Time",
      "release_date": "2015-07-26T00:00:00+00:00",
      "season": 2,
      "series": 1,
      "reviews": [
        {
          "author": "Dr. Arturo Kunze PhD",
          "text": "low‑key — They recycled ideas and somehow made them louder but not better. Could’ve been an email.",
          "publication_date": "2025-03-27T00:00:00+00:00",
          "rating": 3
        }
      ],
      "characters": [
        {
          "name": "Rick Sanchez",
          "gender": "male",
          "status": "alive",
          "url": "https://rickandmortyapi.com/api/character/1"
        }
      ]
    }
  ]
}
</pre>
<h3>Запуск приложения</h3>
Чтобы запустить приложения выполните <code>make init</code>.

API будет доступно по адресу http://localhost:8081/api/v1

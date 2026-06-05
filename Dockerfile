FROM php:8.2-cli
COPY . /app
WORKDIR /app
EXPOSE 8080
CMD php -S 0.0.0.0:${PORT:-8080} -t .

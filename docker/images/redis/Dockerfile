FROM redis:latest

# копируем кастомные конфиги Redis
COPY ./conf.d/redis.conf /usr/local/etc/redis/redis.conf
CMD [ "redis-server", "/usr/local/etc/redis/redis.conf" ]
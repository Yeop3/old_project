FROM caddy:builder AS builder

RUN caddy-builder \
    github.com/abiosoft/caddy-exec \
    github.com/abiosoft/caddy-json-parse \
    github.com/caddy-dns/cloudflare

FROM caddy:alpine

COPY --from=builder /usr/bin/caddy /usr/bin/caddy
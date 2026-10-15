#!/usr/bin/env bash
# Run interactively over SSH. Never put the mailbox password in arguments.
set -euo pipefail
set +x
umask 077
cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.."

if [[ ! -t 0 || ! -t 1 ]]; then
    printf 'Reikia interaktyvaus SSH terminalo (ssh -t).\n' >&2
    exit 1
fi

terminal_state=$(stty -g)
cleanup() {
    stty "$terminal_state" || true
    unset mailbox_password
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM HUP

printf '\nPrijungiama info@devsprendimai.lt prie Hostinger SMTP.\n'
printf 'Slaptazodis nebus rodomas ar irasomas i komandu istorija.\n'
printf 'Iveskite PASTO DEZUTES slaptazodi ir spauskite Enter: '
stty -echo
IFS= read -r mailbox_password
stty "$terminal_state"
printf '\nTikrinamas saugus prisijungimas...\n'

# printf is a Bash builtin: the secret is passed only through stdin, never argv.
printf '%s' "$mailbox_password" | php deployment/setup-hostinger-mail.php --password-stdin
unset mailbox_password

# Humano Access Control

Roles & permissions management for Laravel (Vuexy UI).

## Installation

```bash
composer require idoneo/humano-access-control
```

## Usage

- Routes:
  - `/app/access-roles`
  - `/app/access-permission`

### Permissions matrix (CRUD)

The role editor shows actions in this order: Read, Create, Update, Delete.

- Read: groups `show`, `index`, `list`, `view`
- Create: groups `create`, `store`
- Update: groups `edit`, `update`
- Delete: groups `destroy`, `delete`, `remove`

### Translatable module labels

Each module (permission prefix before the first dot) has a translatable label. Resolution order:

1) Database translations via `TranslationHelper::transGroup($key, 'modules')`
2) Language files (`lang/<locale>.json`) with key `modules.<key>`
3) Fallback: `ucfirst(<key>)`

Example (file-based):

```json
{
  "modules.academy": "Academy",
  "modules.accounting": "Accounting"
}
```

With DB translations, create group `modules` and keys like `academy`, `accounting`, etc.

### Dependencies

This package relies on:

- `spatie/laravel-permission` for roles/permissions
- `yajra/laravel-datatables-oracle` for listings


## Support

If you find this package useful, you can support the maintainer on GitHub.

- Maintainer: [diego-mascarenhas](https://github.com/diego-mascarenhas)

## License

Licensed under the GNU Affero General Public License v3.0 (AGPL‑3.0). See the official text: [GNU AGPLv3](https://www.gnu.org/licenses/agpl-3.0.html).

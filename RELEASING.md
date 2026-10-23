# Releasing Easy Zillow Reviews

Release workflow. Not shipped: excluded from zips by `.gitattributes`.

## Locations

- Git: `https://github.com/BoltonStudios/easy-zillow-reviews` (remote `origin`, branch `master`)
- Local dev site: `~/Local Sites/bstest/app/public/wp-content/plugins/easy-zillow-reviews`
- WordPress.org SVN checkout: `~/Library/CloudStorage/OneDrive-Personal/Work Projects/WordPressDev/Plugins/easy-zillow-reviews`
  (`https://plugins.svn.wordpress.org/easy-zillow-reviews`). Saved SVN credentials are used.
- Staging: `https://boltonstudidev.wpenginepowered.com` (WP Engine)

## 1. Prepare

- [ ] Bump the version in three places: `Version:` header and `EASY_ZILLOW_REVIEWS_VERSION` in `easy-zillow-reviews.php`, and `Stable tag:` in `README.txt`.
- [ ] Changelog entry: `= X.Y.Z =` with `* Date Released: YYYY-MM-DD`. Fold any never-released versions into it.
- [ ] `Tested up to:` uses major.minor only (e.g. `7.1`); Plugin Check flags patch versions.
- [ ] Freemius SDK: update if a new release exists. Drop the official release into `freemius/` unmodified.
- [ ] Block changed? Run `npm run build` and commit `gutenberg/build/`.
- [ ] Commit and push: `git push origin master`.

## 2. Build and test

- [ ] On GitHub, download the repo zip (Code › Download ZIP). `.gitattributes` excludes dev files.
- [ ] Upload it to Freemius as the new version. Each version number can only be used once in Freemius.
- [ ] Download the **free** and **premium** zips from Freemius into `~/Downloads`
      (`easy-zillow-reviews-free.X.Y.Z.zip`, `easy-zillow-reviews-premium.X.Y.Z.zip`).
- [ ] Test on staging; purge WP Engine's cache after deploying (asset URLs keep `?ver=X.Y.Z`).
- [ ] Check the free zip: version is X.Y.Z, `premium/` is absent, no dev files, PHP lints.
      Freemius strips its own `README.md`/`composer.json`, adds `freemius` as a contributor, and reformats the main file; that's expected.

## 3. Publish the free version (WordPress.org)

```sh
V=X.Y.Z
SVN="$HOME/Library/CloudStorage/OneDrive-Personal/Work Projects/WordPressDev/Plugins/easy-zillow-reviews"
T=$(mktemp -d) && unzip -q ~/Downloads/easy-zillow-reviews-free.$V.zip -d "$T"
cd "$SVN" && svn update
rsync -a --delete --exclude .svn "$T/easy-zillow-reviews/" trunk/
svn status trunk | awk '$1=="?"{print $2}' | xargs -I{} svn add -q --parents "{}"
svn status trunk | awk '$1=="!"{print $2}' | xargs -I{} svn rm -q "{}"
svn cp trunk "tags/$V"
svn status | awk '{print $1}' | sort | uniq -c    # review before committing
svn commit -m "Tagging version $V: <summary>."
```

- [ ] Verify: `https://api.wordpress.org/plugins/info/1.0/easy-zillow-reviews.json` shows the new version (within a minute or two).
- [ ] Plugin assets (banners, icons, screenshots) live in SVN `assets/`, separate from trunk; change only when needed.

## 4. Publish Premium (Freemius)

- [ ] Release the version in the Freemius dashboard so paying users get the update.
- [ ] Keep the pricing page's plan features in sync with the readme.

## 5. After release

- [ ] Spot-check the WordPress.org listing (description, changelog, External services).
- [ ] Update `ROADMAP.md` (if present).

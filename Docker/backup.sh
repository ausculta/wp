#! /bin/bash
tar cpzv --exclude=website/assets -f website.tar.gz website

mv shared/website.tar.gz.1 shared/website.tar.gz.2
mv shared/website.tar.gz shared/website.tar.gz.1
mv website.tar.gz shared/website.tar.gz


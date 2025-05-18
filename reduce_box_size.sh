#!/bin/bash -x

set -e

# Remove history files
for home in /root /home/vagrant; do
  rm -f $home/.bash_history \
        $home/.mysql_history \
        $home/.sphinxql_history
done

# Remove apt cache
apt-get clean -y
apt-get autoclean -y
find /var/lib/apt -type f -exec rm -f {} +

# Fill unused disk blocks with zeros
dd if=/dev/zero of=/EMPTY bs=1M || true
rm -f /EMPTY

# Manticore uses a lot of RAM,
# so better shut it down before recreating swap
systemctl stop manticore

# Recreate swap that mostly consists of zeroed blocks
swappart=$(cat /proc/swaps | sed '1d' | awk -F ' ' '{print $1}')
if [ -n "$swappart" ]; then
  uuid=$(blkid -o value -s UUID "$swappart")
  swapoff "$swappart"
  dd if=/dev/zero of="$swappart" || true
  mkswap -U "$uuid" "$swappart"
  swapon "$swappart"
fi

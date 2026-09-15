#!/usr/bin/env python3
"""Sube archivos al docroot de producción vía FTP."""
import ftplib
import os
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
BASE = os.environ.get("FTP_BASE", "/plansaludfacil.cl")


def main() -> int:
    user = os.environ.get("FTP_USER")
    password = os.environ.get("FTP_PASS")
    if not user or not password:
        print("FTP_USER y FTP_PASS requeridos", file=sys.stderr)
        return 1

    files = sys.argv[1:]
    if not files:
        print("Uso: ftp_upload.py <ruta-relativa> ...", file=sys.stderr)
        return 1

    ftp = ftplib.FTP()
    ftp.connect("ftp.plansaludfacil.cl", 21, timeout=30)
    ftp.login(user, password)
    ftp.set_pasv(True)

    for rel in files:
        local = ROOT / rel
        remote_dir = BASE + "/" + str(Path(rel).parent).replace("\\", "/")
        name = Path(rel).name
        parts = [p for p in remote_dir.strip("/").split("/") if p]
        cur = ""
        for part in parts:
            cur += "/" + part
            try:
                ftp.mkd(cur)
            except ftplib.error_perm:
                pass
        ftp.cwd(remote_dir)
        with open(local, "rb") as f:
            ftp.storbinary("STOR " + name, f)
        print(f"OK {rel} -> {remote_dir}/{name} ({ftp.size(name)} bytes)")

    ftp.quit()
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

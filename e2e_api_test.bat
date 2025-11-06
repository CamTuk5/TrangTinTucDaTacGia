@echo off
setlocal EnableDelayedExpansion

REM ==== 0) Cấu hình cơ bản ====
set BASE=http://127.0.0.1:8000
set CAT_NAME=Thoi su
set CAT_SLUG=thoi-su-%RANDOM%
set POST_TITLE=Ban tin sang
set POST_SLUG=ban-tin-sang-%RANDOM%
set POST_CONTENT=Noi dung thu nghiem...

echo === E2E BLOG API TEST ===
echo Base: %BASE%
echo.

REM ==== 1) Login Admin ====
for /f "delims=" %%A in ('powershell -NoProfile -Command ^
  "(Invoke-RestMethod -Method Post -Uri '%BASE%/api/auth/login' -ContentType 'application/json' -Body '{\"email\":\"admin@example.com\",\"password\":\"admin123!\"}').token"') do set "ADMIN=%%A"
if "%ADMIN%"=="" ( echo [FAIL] Login admin & goto :end ) else echo [OK] Admin token got

REM ==== 2) Login Author ====
for /f "delims=" %%A in ('powershell -NoProfile -Command ^
  "(Invoke-RestMethod -Method Post -Uri '%BASE%/api/auth/login' -ContentType 'application/json' -Body '{\"email\":\"author@example.com\",\"password\":\"author123!\"}').token"') do set "AUTHOR=%%A"
if "%AUTHOR%"=="" ( echo [FAIL] Login author & goto :end ) else echo [OK] Author token got

REM ==== 3) Tạo Category ====
echo [STEP] Create category: %CAT_NAME% / %CAT_SLUG%
curl -s -X POST %BASE%/api/categories ^
  -H "Authorization: Bearer %ADMIN%" ^
  -H "Content-Type: application/json" ^
  -d "{\"name\":\"%CAT_NAME%\",\"slug\":\"%CAT_SLUG%\"}" > NUL
echo [OK] Category request sent

REM ==== 4) Author tạo post draft (dùng curl) -> lưu JSON ra file tạm ====
echo [STEP] Create draft post: %POST_TITLE% / %POST_SLUG%
del /q .tmp_post.json 2>NUL
curl -s -X POST %BASE%/api/posts ^
  -H "Authorization: Bearer %AUTHOR%" ^
  -H "Content-Type: application/json" ^
  -d "{\"title\":\"%POST_TITLE%\",\"slug\":\"%POST_SLUG%\",\"content\":\"%POST_CONTENT%\",\"category_id\":1,\"status\":\"draft\"}" > .tmp_post.json

REM ==== Kiểm tra lỗi server/validate nhanh ====
for /f "delims=" %%A in ('powershell -NoProfile -Command ^
  "$j = Get-Content .tmp_post.json -Raw; try { (ConvertFrom-Json $j) | Out-Null; 'OK' } catch { 'ERR' }"') do set "JSON_OK=%%A"
if /i not "%JSON_OK%"=="OK" (
  echo [FAIL] Response khong phai JSON hop le:
  type .tmp_post.json
  goto :end
)

REM ==== Lấy POST_ID & SLUG từ file JSON ====
for /f "delims=" %%A in ('powershell -NoProfile -Command ^
  "(Get-Content .tmp_post.json -Raw | ConvertFrom-Json).id"') do set "POST_ID=%%A"
for /f "delims=" %%A in ('powershell -NoProfile -Command ^
  "(Get-Content .tmp_post.json -Raw | ConvertFrom-Json).slug"') do set "POST_SLUG_CREATED=%%A"

if "%POST_ID%"=="" (
  echo [FAIL] Khong lay duoc POST_ID. Phan hoi:
  type .tmp_post.json
  goto :end
) else (
  echo [OK] Post id: %POST_ID%, slug: %POST_SLUG_CREATED%
)

REM ==== 5) Admin publish ====
echo [STEP] Publish post #%POST_ID%
curl -s -X POST %BASE%/api/posts/%POST_ID%/publish -H "Authorization: Bearer %ADMIN%" > NUL
echo [OK] Publish request sent

REM ==== 6) List published ====
echo [STEP] GET list /api/posts
curl -s %BASE%/api/posts
echo.

REM ==== 7) Detail by slug (dung slug vua tao) ====
echo [STEP] GET detail /api/posts/%POST_SLUG_CREATED%
curl -s %BASE%/api/posts/%POST_SLUG_CREATED%
echo.

echo === DONE ===

:end
endlocal

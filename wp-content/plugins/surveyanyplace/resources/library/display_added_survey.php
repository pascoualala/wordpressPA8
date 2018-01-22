<!-- Here we return the added Surveys in the post.php page from wordPress(backend admin page)-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<div class="test-box"></div>
<div class="mceTmpl">
    <div class="sa-embed-wrapper {{ data.builder && 'is-contact-form' }}" id="sa_{{ data.id }}">
        <div class="sa-content">
            <# if ( data.url ) { #>
                <div class="edit-link">Edit embed settings</div>
                <div class="sa-embed-wrapper__info">
                    <span class="sa-embed-wrapper__label">Url:</span>
                    <span class="sa-embed-wrapper__value">{{ data.url }}</span>
                </div>
                <div class="sa-embed-wrapper__info">
                    <span class="sa-embed-wrapper__label">Type:</span>
                    <span class="sa-embed-wrapper__value">{{ data.type }}</span>
                </div>
                <# } else if ( data.builder ) { #>
                    <div class="edit-link">Edit form settings</div>
                    <div class="sa-embed-wrapper__info">
                        <span class="sa-embed-wrapper__label">Type:</span>
                        <span class="sa-embed-wrapper__value">{{ data.type }}</span>
                    </div>
                    <# } else { #>
                        <span>No URL provided.</span>
                        <# } #>
        </div>
    </div>
</div>
</body>
</html>

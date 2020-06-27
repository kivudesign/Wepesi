<div id="deleteQ" class="w3-modal w3-animate-opacity">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom">
        <div class="w3-card-4">
            <header class="w3-container w3-blue-grey">
                <h1>Prompt</h1>
            </header>
            <div class="w3-container">
                <p>{{message}}</p>
            </div>
            <footer class="w3-container w3-center w3-padding w3-blue-grey">
                <form action="../controller/{{target}}" methode="POST" id="questionForm">
                    <button type="submit" name="{{src}}" value="{{operation}}" class="w3-btn w3-round w3-border" onclick="w3.hide('#deleteQ')">yes</button>
                    <button type="reset" class="w3-btn w3-round w3-border" onclick="w3.hide('#deleteQ')">Cancel</button>
                </form>
            </footer>
        </div>
    </div>
</div>
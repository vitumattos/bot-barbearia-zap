# ============ IMPORTAÇÕES ============ #
from time import sleep
import requests

from webdriver_manager.chrome import ChromeDriverManager
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

import os
from pathlib import Path
from dotenv import load_dotenv
load_dotenv()

# ============ CONSTANTES ============ #
# BASE_DIR = os.getcwd()
BASE_DIR = Path(__file__).parent
CHAVE_API = os.getenv('CHAVE_API')
USER = 'user@email.com'

# ============ Configuração Básica ============ #
chrome_options = Options()
chrome_options.add_argument(r"user-data-dir=" + str(BASE_DIR) + '/profile/whatsapp')

service = Service(ChromeDriverManager().install())

driver = webdriver.Chrome(service=service, options=chrome_options)
driver.get('https://web.whatsapp.com/')
sleep(10)


# ============ Configurações da API ============ #
# ===== EDITARCODIGO ===== #
agent = {
    "User-Agent": 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'}
API_EDITACODIGO = requests.get(url=f"https://editacodigo.com.br/index/api-whatsapp/{CHAVE_API}",  headers=agent)
sleep(1)
API_EDITACODIGO = API_EDITACODIGO.text
API_EDITACODIGO = API_EDITACODIGO.split(".n.")

# ============ Objetos ============ #
bolinha_verde = API_EDITACODIGO[3].strip()  # _ahlk
contato_cliente = API_EDITACODIGO[4].strip()  # //*[@id="main"]/header/div[2]/div/div/div/span
msg_cliente = API_EDITACODIGO[6].strip()  # '_amjv'
caixa_msg1 = API_EDITACODIGO[7].strip()  # 'div[title="Digite uma mensagem"]'

caixa_msg2 = API_EDITACODIGO[5].strip()  # //*[@id="main"]/footer/div[1]/div/span[2]/div/div[2]/div[1]/div/div[1]/p
caixa_pesquisa = API_EDITACODIGO[8].strip()  # "div[title='Caixa de texto de pesquisa']"


# ============ Bot ============ #
def bot():
    try:
        # ===== CLICK NOTIFICAÇÃO ===== #
        bolinha = driver.find_element(By.CLASS_NAME, bolinha_verde)
        bolinha = driver.find_elements(By.CLASS_NAME, bolinha_verde)
        click_bolinha = bolinha[-1]
        action = webdriver.common.action_chains.ActionChains(driver)
        action.move_to_element_with_offset(click_bolinha, 0, -20)
        action.click().perform()
        action.click().perform()

        sleep(0.5)
        # ===== PEGAR CONTATO DO CLIENTE ===== #
        cliente_contato = driver.find_element(By.XPATH, contato_cliente)
        cliente_contato = cliente_contato.text
        print(f'Conversando com  {cliente_contato}')

        sleep(0.5)
        # ===== PEGA A ÚLTIMA MENSAGEM ===== #
        todas_as_msg = driver.find_elements(By.CLASS_NAME, msg_cliente)
        lista_msg_texto = [e.text for e in todas_as_msg]
        cliente_msg = lista_msg_texto[-1]
        cliente_msg = str(cliente_msg).split('\n')[0]
        print(f'A mensagem foi {cliente_msg}')

        sleep(0.5)
        # ===== ENVIA UMA MENSAGEM PELO PHP ===== #
        campo_de_texto = driver.find_element(By.CSS_SELECTOR, caixa_msg1)
        campo_de_texto.click()
        sleep(1)

        resposta = requests.get('http://localhost/BotBarbeariaZap/index.php?',
                                params={'msg': cliente_msg, 'contato': cliente_contato, 'usuario': USER}, headers=agent)
        resposta = resposta.text
        print(resposta)
        sleep(2)
        campo_de_texto.send_keys(resposta, Keys.ENTER)

        sleep(0.5)
        # FECHAR CONTATO
        webdriver.ActionChains(driver).send_keys(Keys.ESCAPE).perform()
    except:
        print('Buscando novas mensagens não lidas!')


# ============ Start ============ #
while True:
    sleep(3)
    bot()

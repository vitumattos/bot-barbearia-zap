from time import sleep
from datetime import date, timedelta
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
BASE_DIR = Path(__file__).parent
CHAVE_API = os.getenv('CHAVE_API')

# ============ Configuração Básica ============ #
chrome_options = Options()
chrome_options.add_argument(r"user-data-dir=" + str(BASE_DIR) + '/profile')

service = Service(ChromeDriverManager().install())

driver = webdriver.Chrome(service=service, options=chrome_options)
driver.get('https://web.whatsapp.com/')
sleep(5)

wait = WebDriverWait(driver=driver, timeout=60)

# ============ Configurações da API ============ #
agent = {
    "User-Agent": 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'}
api = requests.get(url=f"https://editacodigo.com.br/index/api-whatsapp/{CHAVE_API}",  headers=agent)
sleep(1)
api = api.text
api = api.split(".n.")

# ============ Objetos ============ #
bolinha_notificacao = api[3].strip()  # _ahlk
contato_cliente = api[4].strip()  # //*[@id="main"]/header/div[2]/div/div/div/span
caixa_msg3 = api[5].strip()  # //*[@id="main"]/footer/div[1]/div/span[2]/div/div[2]/div[1]/div/div[1]/p
msg_cliente = api[6].strip()

caixa_msg2 = api[7].strip()
caixa_pesquisa = api[8].strip()



# ============ Funções ============ #
def click_notificacao():
    bolinhas = driver.find_elements(By.CLASS_NAME, bolinha_notificacao)
    click_bolinha = bolinhas[-1]
    action = webdriver.common.action_chains.ActionChains(driver)
    action.move_to_element_with_offset(click_bolinha, 0, -20)
    action.click()
    action.perform()
    action.click()
    action.perform()
    sleep(1)
    return True


def pegar_telefone():
    telefone_cliente = driver.find_element(By.XPATH, contato_cliente)
    telefone_final = telefone_cliente.text
    sleep(2)

    return telefone_final


def pegar_ultima_mensagem():
    todas_as_msg = driver.find_elements(By.CLASS_NAME, msg_cliente)
    todas_as_msg_texto = [e.text for e in todas_as_msg]
    msg = todas_as_msg_texto[-5]
    msg = str(msg).split('\n')[0]
    sleep(2)

    return msg


def bem_vindo():
    campo_de_texto = driver.find_element(By.XPATH, caixa_msg3)
    campo_de_texto.click()
    sleep(1)
    campo_de_texto.send_keys("Bem vindo a Barbearia dois irmão", Keys.ENTER)

    campo_de_texto = driver.find_element(By.XPATH, caixa_msg3)
    campo_de_texto.click()
    sleep(1)
    campo_de_texto.send_keys("R$20,00....Corte Básico", Keys.SHIFT + Keys.ENTER)
    campo_de_texto.send_keys("R$25,00....Corte Navalhado", Keys.SHIFT + Keys.ENTER)
    campo_de_texto.send_keys("R$5,00......Só o pezinho", Keys.SHIFT + Keys.ENTER)
    campo_de_texto.send_keys("R$5,00......Sobrancelha", Keys.SHIFT + Keys.ENTER)
    campo_de_texto.send_keys("R$10,00....Barba", Keys.SHIFT + Keys.ENTER)
    campo_de_texto.send_keys("R$30,00....Combo cabelo, barba e Sobrancelha", Keys.SHIFT + Keys.ENTER)
    campo_de_texto.send_keys("R$5,00......Desenhos e Listras", Keys.ENTER)

    campo_de_texto = driver.find_element(By.XPATH, caixa_msg3)
    campo_de_texto.click()
    sleep(1)
    campo_de_texto.send_keys("Rua Quize de Novembro, 378 - Praia de Mauá" + Keys.ENTER)


def mensagem_qualquer(msg1):
    campo_de_texto = driver.find_element(By.XPATH, caixa_msg3)
    campo_de_texto.click()
    sleep(2)
    campo_de_texto.send_keys(msg1, Keys.ENTER)


# ============ Bot ============ #
def bot():
    try:
        if click_notificacao():
            mensagem_qualquer(f'Olá {pegar_telefone()}')
            bem_vindo()
            mensagem_qualquer(f'Sua mensagem "{pegar_ultima_mensagem()}" é inutil')
            mensagem_qualquer(f'Fim!!')

            # FECHAR CONTATO
            webdriver.ActionChains(driver).send_keys(Keys.ESCAPE).perform()
    except:
        print('Não há mensagens')


# ============ Start ============ #
while True:
    sleep(5)
    bot()

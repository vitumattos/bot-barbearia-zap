# ============ IMPORTAÇÕES ============ #
from time import sleep
import requests
import json

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
wait = WebDriverWait(driver, 10)
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

caixa_msg2 = '//*[@id="main"]/footer/div[1]/div/span[2]/div/div[2]/div[1]/div/div[1]/p'
caixa_pesquisa = API_EDITACODIGO[8].strip()  # "div[title='Caixa de texto de pesquisa']"


# ===== Script JS ===== #
def paste_content(driver, el, content):
    driver.execute_script(
        f'''
const text = `{content}`;
const dataTransfer = new DataTransfer();
dataTransfer.setData('text', text);
const event = new ClipboardEvent('paste', {{
  clipboardData: dataTransfer,
  bubbles: true
}});
arguments[0].dispatchEvent(event)
''',
        el)


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
        campo_de_texto = wait.until(EC.element_to_be_clickable((By.XPATH, caixa_msg2)))
        campo_de_texto.click()
        sleep(0.5)

        resposta = requests.get('http://localhost/BotBarbeariaZap/index.php?',
                                params={'msg': cliente_msg, 'contato': cliente_contato, 'usuario': USER}, headers=agent)
        resposta = resposta.text
        resposta = json.loads(resposta)
        print(resposta)
        if 'primeira vez' in resposta['status']:
            paste_content(driver, campo_de_texto, '🚀 Olá!')
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(
                "Bem-vindo à *Barbearia Dois Irmão!* Aqui é onde os cabelos recebem o tratamento *VIP* que merecem! ")
            paste_content(driver, campo_de_texto, '💈✨')
            campo_de_texto.send_keys(' Vamos fazer você se sentir incrível!', Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys('*Como gostaria de ser chamado?*', Keys.ENTER)

        elif 'principal' in resposta['menu']:
            nome = resposta['nome'].capitalize()
            frase = resposta['frase'].capitalize()
            paste_content(driver,campo_de_texto,'☠️✂️')
            campo_de_texto.send_keys(f'{frase}, *{nome}*.',Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            
            campo_de_texto.send_keys("*MENU PRINCIPAL*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'1️⃣')
            campo_de_texto.send_keys(" - *ENTRAR NA FILA*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'2️⃣')
            campo_de_texto.send_keys(" - *VER TEMPO DE ESPERA*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'3️⃣')
            campo_de_texto.send_keys(" - *VER PREÇO E PROMOÇÕES*", Keys.ENTER)

        elif 'fila' in resposta['case']:
            paste_content(driver,campo_de_texto,'☠️✂️')
            fila = json.loads(resposta['fila'])
            campo_de_texto.send_keys(f"Há {fila['qtd_fila']} pessoas na fila de espera",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            for i in range(fila['qtd_fila']):
                campo_de_texto.send_keys(f"{i+1} - *{fila['nome'][i]}* ({fila['telefone'][i]})",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.ENTER)
            sleep(1)
            campo_de_texto = wait.until(EC.element_to_be_clickable((By.XPATH, caixa_msg2)))
            campo_de_texto.click()  
            campo_de_texto.send_keys(f"Tempo estimado na fila é de {20*fila['qtd_fila']}",Keys.ENTER)

        elif 'tabela' in resposta['case']:
            paste_content(driver,campo_de_texto,'☠️✂️')
            campo_de_texto.send_keys("Olha nossa tabela de preços",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("*R$20,00*....Corte Básico",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("*R$25,00*....Corte Navalhado",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("*R$5,00*......Só o pezinho",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("*R$5,00*......Sobrancelha",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("*R$10,00*....Barba",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("*R$5,00*......Desenhos e Listras",Keys.ENTER)
            sleep(1)
            campo_de_texto = wait.until(EC.element_to_be_clickable((By.XPATH, caixa_msg2)))
            campo_de_texto.click()  
            paste_content(driver,campo_de_texto,'☠️✂️')
            campo_de_texto.send_keys("Olha só essas promoções",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            paste_content(driver, campo_de_texto, '💈')
            campo_de_texto.send_keys("COMBO 1 - *PRIMEIRO* *CLIENTE*",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("_(Se você for o primeiro a ser atendido no dia, você ganha 2,00 de desconto)_",Keys.SHIFT + Keys.ENTER)
            paste_content(driver, campo_de_texto, '💵')
            campo_de_texto.send_keys(" De ~*R$ 20,00*~  por *R$18,00*",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            paste_content(driver, campo_de_texto, '💈')
            campo_de_texto.send_keys("COMBO 2 - *BARBA-CABELO-BIGODE*",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("_(Corte completo de Cabelo, Barba e Bigode)_",Keys.SHIFT + Keys.ENTER)
            paste_content(driver, campo_de_texto, '💵')
            campo_de_texto.send_keys(" De ~*R$ 30,00*~  por *R$25,00*",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            paste_content(driver, campo_de_texto, '⌛')
            campo_de_texto.send_keys(" Promoções válidas *APENAS* para agendamentos marcados pelo Whatsapp até o final desta semana",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("APROVEITE", Keys.ENTER)

        elif 'confirmacao' in resposta['case']:
            if 'info' in resposta ['frase']:
                paste_content(driver,campo_de_texto,'☠️✂️')
                campo_de_texto.send_keys("*ATENÇÃO*",Keys.SHIFT + Keys.ENTER)
                campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
                campo_de_texto.send_keys("Para garantirmos um atendimento eficiente, solicitamos que chegue com *10 minutos* de antecedência ao salão. Além disso, concederemos uma tolerância de mais *5 minutos* após o horário previsto.",Keys.SHIFT + Keys.ENTER)
                
                campo_de_texto.send_keys(Keys.ENTER)
            elif 'nao entendi' in resposta['frase']:
                campo_de_texto.send_keys("Utilize os índeces",Keys.SHIFT + Keys.ENTER)

            sleep(1)
            campo_de_texto = wait.until(EC.element_to_be_clickable((By.XPATH, caixa_msg2)))
            campo_de_texto.click() 
            paste_content(driver,campo_de_texto,'☠️✂️')
            campo_de_texto.send_keys("Deseja entrar na fila",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("1 - *Sim*",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("2 - *Não*",Keys.ENTER)

        elif 'encerrar' in resposta['case']:
            paste_content(driver,campo_de_texto,'☠️✂️')
            campo_de_texto.send_keys("Obrigado pelo seu tempo.",Keys.SHIFT + Keys.ENTER)

        elif 'entrar' in resposta['case']:
            paste_content(driver,campo_de_texto,'☠️✂️')
            campo_de_texto.send_keys("Você acaba de entrar na fila. Lembrando que a tolerância é até 5 minutos do horário",Keys.
            ENTER)

        elif '5' in resposta['status']:
            paste_content(driver,campo_de_texto,'☠️✂️')
            campo_de_texto.send_keys("Você já estpa na fila.",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys("Digite: *Sair* p/ sair da fila",Keys.ENTER)

        # ===== SUPER USER ===== #
        elif resposta['menu'] == "admin":
            if resposta['case'] == 'concluido':
                campo_de_texto.send_keys(f"{resposta['nome']} Seu corte foi concluido",Keys.SHIFT + Keys.ENTER)
                campo_de_texto.send_keys(f"CHAVE PIX AQUI",Keys.ENTER)

            if resposta['case'] == 'concluido':
                campo_de_texto.send_keys(f"{resposta['nome']}foi removido da fila",Keys.ENTER)

            if resposta['case'] == 'default':
                campo_de_texto.send_keys("Utilize os índices",Keys.ENTER)

            if resposta['case'] == 'add_fila':
                campo_de_texto.send_keys(f"{resposta['nome']} foi adicionado na fila",Keys.ENTER)

            sleep(1)
            campo_de_texto = wait.until(EC.element_to_be_clickable((By.XPATH, caixa_msg2)))
            campo_de_texto.click()   
            campo_de_texto.send_keys("*Menu de gerenciamento*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'0️⃣')
            campo_de_texto.send_keys(" - *SAIR*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'1️⃣')
            campo_de_texto.send_keys(" - *LISTAR FILA*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'2️⃣')
            campo_de_texto.send_keys(" - *LISTAR CLIENTES*", Keys.ENTER)

        elif resposta['case'] == '0' and resposta['status'] == '100':
            campo_de_texto.send_keys("*Encerrado*",Keys.ENTER)

        elif resposta['case'] == '1' and resposta['status'] == '100':
            fila = json.loads(resposta['fila'])
            if 'out' in resposta['frase']:
                campo_de_texto.send_keys("Utilize os índices",Keys.SHIFT + Keys.ENTER)  
        
            campo_de_texto.send_keys("*Fila atual*",Keys.SHIFT + Keys.ENTER)  
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)

            campo_de_texto.send_keys("0 - *VOLTAR*",Keys.SHIFT + Keys.ENTER)  
            for i in range(fila['qtd_fila']):
                campo_de_texto.send_keys(f"{i+1} - *{fila['nome'][i]}* ({fila['telefone'][i]})",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.ENTER)

        elif resposta['case'] == '2' and resposta['status'] == '100':
            fila = json.loads(resposta['fila'])
            if 'out' in resposta['frase']:
                campo_de_texto.send_keys("Utilize os índices",Keys.SHIFT + Keys.ENTER)  
        
            clientes = json.loads(resposta['fila'])
            campo_de_texto.send_keys("*Clientes cadastrados*",Keys.SHIFT + Keys.ENTER)  
            campo_de_texto.send_keys(Keys.SHIFT + Keys.ENTER)

            campo_de_texto.send_keys("0 - *VOLTAR*",Keys.SHIFT + Keys.ENTER)  
            for i in range(len(clientes)):
                campo_de_texto.send_keys(f"{i+1} - *{clientes[i]['nome']}* ({clientes[i]['telefone']}) - status: {clientes[i]['status']}",Keys.SHIFT + Keys.ENTER)
            campo_de_texto.send_keys(Keys.ENTER)

        elif 'admin_lista' in resposta['menu']:
            if resposta['case'] == 'out':
                campo_de_texto.send_keys("Utilize os índices",Keys.SHIFT + Keys.ENTER)
            
            campo_de_texto.send_keys(f"*Menu de fila do* {resposta['frase']}",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'0️⃣')
            campo_de_texto.send_keys(" - *SAIR*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'1️⃣')
            campo_de_texto.send_keys(" - *CONCLUIR CORTE*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'2️⃣')
            campo_de_texto.send_keys(" - *REMOVER DA FILA*", Keys.ENTER)

        elif resposta['menu'] == 'admin_cliente':
            campo_de_texto.send_keys(f"*Menu do cliente* {resposta['frase']}",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'0️⃣')
            campo_de_texto.send_keys(" - *SAIR*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'1️⃣')
            campo_de_texto.send_keys(" - *ADICIONAR NA FILA*",Keys.SHIFT + Keys.ENTER)
            paste_content(driver,campo_de_texto,'2️⃣')
            campo_de_texto.send_keys(" - *EDITA NOME*", Keys.ENTER)

        elif resposta['status'] == '102.1' and resposta['case'] == '2':
            campo_de_texto.send_keys(f"Qual será o novo nome de {resposta['nome']}?",Keys.ENTER)
            
        sleep(1)
        # FECHAR CONTATO
        webdriver.ActionChains(driver).send_keys(Keys.ESCAPE).perform()
    except:
        print('Buscando novas mensagens não lidas!')


# ============ Start ============ #
if __name__ == "__main__":
    while True:
        sleep(2)
        bot()
